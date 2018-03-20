<?php

// +----------------------------------------------------------------------
// | VMCSHOP [V M-Commerce Shop]
// +----------------------------------------------------------------------
// | Copyright (c) vmcshop.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.vmcshop.com/licensed)
// +----------------------------------------------------------------------
// | Author: Shanghai ChenShang Software Technology Co., Ltd.
// +----------------------------------------------------------------------


class o2ocds_service_order_sendfinish
{
    public function __construct($app)
    {
        $this->tb_prefix = vmc::database()->prefix;
        $this->app = $app;
        $this->app_b2c = app::get('b2c');
    }

    /*
     * 订单发货，增加账户实际佣金
     */
    public function exec($delivery_sdf, &$msg)
    {
        if($this->app->getConf('trigger_type') != '1') {
            return true;
        };
        $ectools_math = vmc::singleton('ectools_math');
        $order_id = $delivery_sdf['order_id'];
        $mdl_service_code = $this->app->model('service_code');
        $service_code = $mdl_service_code->getRow('*',array('order_id'=>$order_id));

        $mdl_relation = $this->app->model('relation');
        $relation_filter = array('member_id'=>$delivery_sdf['member_id'],'type'=>'store');
        //拥有店铺权限，且会员等级是渠道价等级的会员，忽略店铺分佣。但经销商按照“销售价（非成交价）”分佣。
        if($mdl_service_code->member_channelprice($delivery_sdf['member_id']) && $relation = $mdl_relation->getRow('*',$relation_filter)) {
            $is_channelprice = true;
            $service_code['store_id'] = '';
            $enterprise_id = $this->app->model('store')->getRow('enterprise_id',array('store_id'=>$relation['relation_id']));
            $service_code['enterprise_id'] = $enterprise_id['enterprise_id'];
        }elseif(!$service_code['store_id'] && !$service_code['enterprise_id']) {//没有服务码，不是推广进来的无需进行分佣计算
            return true;
        }

        /*$orderlog = $this->app->model('orderlog')->count(array('order_id' => $order_id));
        if ($orderlog) {
            $msg = "该订单佣金已清算过";
            return false;
        }*/
        if(!$delivery_sdf['delivery_items']){
            $delivery_sdf['delivery_items'] = app::get('b2c')->model('delivery_items')->getList('*',array('delivery_id'=>$delivery_sdf['delivery_id']));
        }
        $delivery_items = utils::array_change_key($delivery_sdf['delivery_items'],'order_item_id');
        $order_item_id = array_keys($delivery_items);
        //按成交价计算
        $delivery_order_items = $this->app_b2c->model('order_items')->getList("item_id,order_id,product_id ,goods_id ,price,buy_price ,amount ,nums,spec_info,bn,name,image_id",
            array('item_id' => $order_item_id,'item_type'=>'product'));
        $order_items = array();
        foreach($delivery_order_items as   $items) {
            $items['nums'] = $delivery_items[$items['item_id']]['sendnum'];
            if($is_channelprice) { //是渠道价 经销商按照“销售价（非成交价）”分佣
                $items['buy_price'] = $items['price'];
            }
            $items['amount'] = $ectools_math->number_multiple(array(
                $items['nums'],
                $items['buy_price']
            ));
            $order_items[$items['product_id']] = $items;
        }
        $order_items = utils::array_change_key($order_items, "product_id");
        $orderlog_datail =array();

        foreach(vmc::servicelist('o2ocds.mode') as $service){
            $orderlog_datail = $service ->create($order_items ,$service_code,$delivery_sdf);
            if($orderlog_datail){
                break;
            }
        }
        if(!$orderlog_datail){
            return true;
        }
        $orderlog = array(
            'order_id' => $order_id,
            'from_id' => $delivery_sdf['member_id'],
            'settle' => 0, //未结算
            'order_fund' => $orderlog_datail['order_fund'],
            'items' => $orderlog_datail['items'],
            'achieve' => $orderlog_datail['achieve'],
            'createtime' => time()
        );

        if (false == $this->app->model('orderlog')->save($orderlog)) {
            $msg = "佣金写入失败";
            return false;
        }
        unset($v);
        //单品佣金统计
        if (false == $this->_products_count($orderlog_datail['items'], $msg)) {
            return false;
        }
        return true;
    }

    /*
     * 单品佣金统计
     */
    private function  _products_count($order_items, &$msg)
    {
        $tb_prefix = vmc::database()->prefix . "o2ocds_";
        $sql = "INSERT INTO {$tb_prefix}products_count(product_id ,o2ocds_total) VALUES";
        $i = 0;
        foreach ($order_items as $k => $v) {
            $sql .= ($i == 0 ? '' : ',') . "({$v['product_id']} ,{$v['product_fund']} )";
            $i++;
        }
        $sql .= "ON DUPLICATE KEY UPDATE o2ocds_total= o2ocds_total +VALUES(o2ocds_total)";
        $re = vmc::database()->exec($sql);
        if (!$re) {
            $msg = "佣金统计失败";

            return false;
        }

        return true;
    }

}