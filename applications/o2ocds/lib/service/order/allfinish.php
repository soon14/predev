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
class o2ocds_service_order_allfinish
{
    public function __construct($app)
    {
        $this->tb_prefix = vmc::database()->prefix;
        $this->app = $app;
        $this->app_b2c = app::get('b2c');
    }

    /*
     * 订单完成，增加账户实际佣金
     */
    public function exec($order, &$msg)
    {
        if($this->app->getConf('trigger_type') != '2') {
            return true;
        };
        $order_id = $order['order_id'];
        $service_code = $this->app->model('service_code')->getRow('*',array('order_id'=>$order_id));
        //没有二维码，不是推广进来的无需进行分佣计算
        if(!$service_code['qrcode_id']) {
            return true;
        }
        //未知二维码，不是推广进来的无需进行分佣计算
        if(!$qrcode = $this->app->model('qrcode')->getRow('*',array('qrcode_id'=>$service_code['qrcode_id']))) {
            return true;
        };

        $orderlog = $this->app->model('orderlog')->count(array('order_id' => $order_id));
        if ($orderlog) {
            $msg = "该订单佣金已清算过";
            return false;
        }

        //按成交价计算
        $order_items = $this->app_b2c->model('order_items')->getList("order_id,product_id ,goods_id ,buy_price ,amount ,nums,spec_info,bn,name,image_id",
            array('order_id' => $order_id));
        $order_items = utils::array_change_key($order_items, "product_id");

        $orderlog_datail =array();
        foreach(vmc::servicelist('o2ocds.mode') as $service){
            $orderlog_datail = $service ->create($order_items ,$qrcode);
            if($orderlog_datail){
                break;
            }
        }
        if(!$orderlog_datail){
            return true;
        }

        $orderlog = array(
            'order_id' => $order_id,
            'from_id' => $order['member_id'],
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
        //上级和上上级...资金变动
        $member_service = vmc::singleton('o2ocds_service_member');
        foreach ($orderlog_datail['achieve'] as $k => $v) {
            $fund_data = array(
                'relation_id' => $v['relation_id'],
                'relation_type' => $v['type'],
                'change_fund' => 0, //实际变动
                'type' => '1',
                'opt_id' => '',
                'opt_type' => 'system',
                'mark' => "佣金收入",
                'frozen_change' => +$v['achieve_fund'],
                'extfield' => $order_id
            );
            if (false == $member_service->fund_change($fund_data, $msg)) {
                return false;
            }
        }
        unset($v);
        //单品佣金统计
        if (false == $this->_products_count($orderlog_datail['items'], $msg)) {
            return false;
        }
        return true;
    }

    /*
     * 单品已结算佣金统计
     */
    private function  _products_count($order_items, &$msg)
    {
        $tb_prefix = vmc::database()->prefix . "o2ocds_";
        $ids = implode(',' , array_keys(utils::array_change_key( $order_items ,"product_id")));
        $sql = "UPDATE {$tb_prefix}products_count SET  o2ocds_settle= case product_id ";
        foreach ($order_items as $k => $v) {
            $sql .= sprintf("WHEN %d THEN o2ocds_settle+%f ", $v['product_id'], $v['product_fund']);
        }
        $sql .= "END WHERE product_id IN ($ids)";
        $re = vmc::database()->exec($sql);
        if (!$re) {
            $msg = "佣金统计失败";

            return false;
        }

        return true;
    }
}