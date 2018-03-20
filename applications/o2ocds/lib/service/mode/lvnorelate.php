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
class o2ocds_service_mode_lvnorelate{
    private $parent_type;
    public function __construct($app){
        $this->app = $app;
        $this->app_b2c = app::get('b2c');
        $this ->parent_type = array( 'enterprise','store');
    }
    /*
     * 分佣模式1
     * 分佣值与会员等级无关
     */
    public function create($order_items ,$service_code,$delivery_sdf){
        $ectools_math = vmc::singleton('ectools_math');
        //全局佣金设置
        foreach($this ->parent_type as $parent){
            $base_o2ocds[$parent] =  $this->app->getConf($parent.'_ratio');
        }
        //product 单品佣金设置
        $o2ocds = $this->app->model('products_extend')->getList('product_id ,o2ocds_value',
            array("product_id" => array_keys($order_items)));
        $o2ocds = utils::array_change_key($o2ocds, "product_id");
        foreach ($order_items as $k => $v) {
            $type_o2ocds = $this->app->model('products_extend')->get_o2ocds_value($order_items[$k]['goods_id']);
            foreach($this ->parent_type as $parent){
                $order_items[$k]['o2ocds'][$parent] = array(
                    $o2ocds[$k]['o2ocds_value'][$parent] ? $o2ocds[$k]['o2ocds_value'][$parent] : ($type_o2ocds[$parent] ? $type_o2ocds[$parent] : $base_o2ocds[$parent]),
                    $o2ocds[$k]['o2ocds_value'][$parent] ? 'product' : ($type_o2ocds[$parent] ? 'type' : 'base')
                );
                //o2ocds_value>=1 ,则不为比例
                if($order_items[$k]['o2ocds'][$parent][0] < 1) {
                    $order_items[$k][$parent.'_o2ocds'] = $ectools_math->number_plus(
                        array(
                            $ectools_math->number_multiple(array(
                                $v['amount'],
                                $order_items[$k]['o2ocds'][$parent][0],
                            )),
                            $order_items[$k][$parent.'_o2ocds']
                        )
                    );
                }else{
                    $order_items[$k][$parent.'_o2ocds'] = $ectools_math->number_plus(
                        array(
                            $ectools_math->number_multiple(array(
                                $v['nums'],
                                $order_items[$k]['o2ocds'][$parent][0]
                            )),
                            $order_items[$k][$parent.'_o2ocds']
                        )
                    );
                };
            }
        }
        unset($k ,$v);
        $order_o2ocds = array();//订单佣金
        foreach($this ->parent_type as $parent){
            foreach($order_items as $k => $v){
                $order_o2ocds[$parent.'_o2ocds'] = $ectools_math->number_plus(
                    array(
                        $order_items[$k][$parent.'_o2ocds']?:0,
                        $order_o2ocds[$parent.'_o2ocds']?:0
                    )
                );
            }
            $order_o2ocds['all_o2ocds'] = $ectools_math->number_plus(
                array(
                    $order_o2ocds[$parent]?:0,
                    $order_o2ocds['all_o2ocds']?:0
                )
            );
        }
        unset($k ,$v);

        //订单佣金流向
        $mdl_achieve = $this->app->model('orderlog_achieve');
        $time = time();
        if($service_code['enterprise_id']) {
            $enterpirse_achieve = array(
                'achieve_id' => $mdl_achieve->apply_id(),
                'relation_id' => $service_code['enterprise_id'],
                'achieve_fund' => $order_o2ocds['enterprise_o2ocds'],
                'type' => 'enterprise',
                'order_id' => $delivery_sdf['order_id'],
                'delivery_id' => $delivery_sdf['delivery_id'],
                'createtime'=>$time
            );;
            $orderlog_achieve[] = $enterpirse_achieve;
        }
        if($service_code['store_id']) {
            $orderlog_achieve[] = array(
                'achieve_id' => $mdl_achieve->apply_id($enterpirse_achieve['achieve_id']),
                'relation_id' => $service_code['store_id'],
                'achieve_fund' => $order_o2ocds['store_o2ocds'],
                'type' => 'store',
                'order_id' => $delivery_sdf['order_id'],
                'delivery_id' => $delivery_sdf['delivery_id'],
                'createtime'=>$time
            );
        }
        $order_fund = $ectools_math->number_plus(
            $order_o2ocds['enterprise_o2ocds']?:0,
            $order_o2ocds['store_o2ocds']?:0
        );
        //订单佣金明细
        foreach ($order_items as $k => $v) {
            for($i = 0 ;$i <2 ;$i++){
                $order_items[$k]['o2ocds_items'][$this ->parent_type[$i]] = $v[$this ->parent_type[$i].'_o2ocds'];
                $order_items[$k]['product_fund'] = $ectools_math->number_plus(
                    $order_items[$k]['o2ocds_items'][$this ->parent_type[$i]]?:0,
                    $order_items[$k]['product_fund']
                );
            }
            //$order_items[$k]['goods_id'];
            unset( $order_items[$k]['amount']);
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