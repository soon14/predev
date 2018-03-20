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
class commission_service_mode_lvnorelate{
    private $parent_type;
    public function __construct($app){
        $this->app = $app;
        $this->app_b2c = app::get('b2c');
        $this ->parent_type = array( 'first','second');
    }
    /*
     * 分佣模式1
     * 分佣值与会员等级无关
     */
    public function create($order_items ,$member_info){
        if($this ->app ->getConf('mode') != 1 ){
            return false;
        }
        //全局佣金设置
        foreach($this ->parent_type as $parent){
            $base_commission[$parent] =  $this->app->getConf($parent.'_ratio');
        }
        //product 单品佣金设置
        $commission = $this->app->model('products_extend')->getList('product_id ,commission_value',
            array("product_id" => array_keys($order_items)));
        $commission = utils::array_change_key($commission, "product_id");

        foreach ($order_items as $k => $v) {
            $type_commission = $this->app->model('products_extend')->get_commission_value($order_items[$k]['goods_id']);
            foreach($this ->parent_type as $parent){
                $order_items[$k]['commission'][$parent] = array(
                    $commission[$k]['commission_value'][$parent] ? $commission[$k]['commission_value'][$parent] : ($type_commission[$parent] ? $type_commission[$parent] : $base_commission[$parent]),
                    $commission[$k]['commission_value'][$parent] ? 'product' : ($type_commission[$parent] ? 'type' : 'base')
                );
                //commission_value>=1 ,则不为比例
                $order_items[$k][$parent.'_commission'] += ($order_items[$k]['commission'][$parent][0] < 1 ? $v['amount'] * $order_items[$k]['commission'][$parent][0] :$v['nums']*$order_items[$k]['commission'][$parent][0]);
            }
        }
        unset($k ,$v);
        $order_commission = array();//订单佣金
        foreach($this ->parent_type as $parent){
            foreach($order_items as $k => $v){
                $order_commission[$parent.'_commission'] += $order_items[$k][$parent.'_commission'];
            }
            $order_commission['all_commission'] += $order_commission[$parent];
        }
        unset($k ,$v);

        $parent_node = explode(',', $member_info['parent_path']);//上级，上上级
        foreach($parent_node as $_k =>$_v){
            if($_v <1){
                unset($parent_node[$_k]);
            }
        }
        $parent_count = count($parent_node);
        $order_fund = 0;
        //订单佣金流向
        $orderlog_achieve = array();
        for ($i = 0; $i < $parent_count; $i++) {
            $parent_type = $this ->parent_type[$i];
            $orderlog_achieve[$i]['member_id'] = $parent_node[$i];
            $orderlog_achieve[$i]['achieve_fund'] = $order_commission[$parent_type.'_commission'];
            $orderlog_achieve[$i]['parent_type'] = $parent_type;
            $order_fund +=  $orderlog_achieve[$i]['achieve_fund'];
        }

        //订单佣金明细
        foreach ($order_items as $k => $v) {
            for($i = 0 ;$i <$parent_count ;$i++){
                $order_items[$k]['commission_items'][$this ->parent_type[$i]] = $v[$this ->parent_type[$i].'_commission'];
                $order_items[$k]['product_fund'] += $order_items[$k]['commission_items'][$this ->parent_type[$i]];
            }
            unset($order_items[$k]['goods_id'], $order_items[$k]['amount']);
        }
        unset($v);
        //订单佣金基础信息
        $orderlog = array(
            'order_fund' => $order_fund,
            'items' => $order_items,
            'achieve' => $orderlog_achieve
        );
        return $orderlog;

    }

}