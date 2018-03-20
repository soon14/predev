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

class restrict_check
{

    public function __construct($app)
    {
        $this->member_id = vmc::singleton('b2c_user_object')->get_member_id();
    }

    /*
     * 检查该用户是否可以购买该商品
     * @param 货品信息 goods_id && product_id  array
     * @msg   错误信息
     * */
    public function get_row($product,&$msg = '') {
        if(!$product['goods_id'] || !$product['product_id']) {
            $msg = '未知商品';
            return false;
        }
        $mdl_restrict = app::get('restrict')->model('restrict');

        //先按照货品优先级进行查询是否有限购活动
        if($restrict = $mdl_restrict->product_restrict($product['product_id'],$this->member_id)) {
            return $restrict;
        };

        //商品查询是否有限购活动
        if($restrict = $mdl_restrict->goods_restrict($product['goods_id'],$this->member_id)) {
            return $restrict;
        };
        return true;
    }

    /*
     * 检查该用户是否可以购买该商品  提供购物车使用
     * @param   货品信息
     * @param   错误信息
     * @param   禁用不查询
     * @param   是否按照商品货品进行分组
     * */
    public function get_list($product_list,&$msg,$disabled = false,$group = false) {
        if(!is_array($product_list)) {
            $msg = '未知商品';
            return false;
        }
        $condition = array();
        foreach($product_list as $k=>$v) {
            if($disabled && $v['disabled'] == 'true') {
                continue;
            }
            $condition[$k]['product_id'] = $v['item']['product']['product_id']?:$v['product_id'];
            $condition[$k]['goods_id'] = $v['item']['product']['goods_id']?:$v['goods_id'];
        }
        $mdl_restrict = app::get('restrict')->model('restrict');
        //先按照货品优先级进行查询是否有限购活动
        $product_ids = array_keys(utils::array_change_key($condition,'product_id'));
        if($restrict = $mdl_restrict->product_restrict($product_ids,$this->member_id)) {
            $restrict = utils::array_change_key($restrict,'product_id');
            if(!$group) {
                $restrict_product_ids = array_keys($restrict);
                $goods_condition = array();
                foreach($condition as $v) {
                    if(in_array($v['product_id'],$restrict_product_ids)) {
                        continue;
                    }
                    $goods_condition[$v['goods_id']][] = $v;
                }
            }else{
                $goods_condition = array();
                foreach($condition as $v) {
                    $goods_condition[$v['goods_id']][] = $v;
                }
            }
        }else{
            $goods_condition = array();
            foreach($condition as $v) {
                $goods_condition[$v['goods_id']][] = $v;
            }
        };
        if($goods_condition) {
            $goods_ids = array_keys($goods_condition);
        }
        //商品查询是否有限购活动
        $restrict_goods = $mdl_restrict->goods_restrict($goods_ids,$this->member_id);
        if(!$group) {
            $ret_restrict = $restrict;
            if($restrict_goods) {
                foreach($restrict_goods as $k=>$v) {
                    if(is_array($goods_condition[$v['goods_id']])) {
                        foreach($goods_condition[$v['goods_id']] as $product){
                            $v['product_id'] = $product['product_id'];
                            $ret_restrict[$product['product_id']] = $v;
                        }
                    }
                }
            }

        }else{
            $ret_restrict['product'] = $restrict;
            if($restrict_goods) {
                foreach($restrict_goods as $k=>$v) {
                    if(is_array($goods_condition[$v['goods_id']])) {
                        $ret_restrict['goods'][$v['goods_id']] = $v;
                    }
                }
            }
        }
        return $ret_restrict;
    }

    /*
     * 检查该订单是否商品是否超过限购数量
     * @param 购物车数据
     * @msg   错误信息
     * */
    public function check($cart,&$msg) {
        $goods_list = $cart['objects']['goods'];
        $restrict_info = $this->get_list($goods_list,$msg,true,true);
        $restrict_product = $restrict_info['product'];
        $restrict_goods = $restrict_info['goods'];
        $mdl_member_log = app::get('restrict')->model('member_log');

        $goods_quantity  = array();
        foreach($goods_list as  $goods) {
            $product_id = $goods['item']['product']['product_id'];
            $goods_id = $goods['item']['product']['goods_id'];
            if($goods['disabled'] == 'true' || (!($res_id = $restrict_product[$product_id]['res_id']) && !($res_id = $restrict_goods[$goods_id]['res_id']))) {
                continue;
            }

            $goods_quantity[$goods['item']['product']['goods_id']] += $goods['quantity'];
            $goods_sum = $goods_quantity[$goods['item']['product']['goods_id']];

            //判断 订单购买数量是否超购
            if($restrict_product[$product_id]) {
                if(($goods['quantity'] > $restrict_product[$product_id]['order_limit']) && ($restrict_product[$product_id]['order_limit'] != 0)) {
                    $msg = '限购商品不能超额购买';
                    return false;
                }
            }
            if($goods_sum > $restrict_goods[$goods_id]['order_limit'] && $restrict_goods[$goods_id]['order_limit'] != 0) {
                $msg = '限购商品不能超额购买';
                return false;
            }

            $filter = array(
                'res_id' => $res_id,
                'member_id' => $this->member_id,
            );

            //判断货品 用户每天限购次数  /*设置了用户限购次数，用户每天限购数量 失效*/
            if($restrict_product[$product_id] && $restrict_product[$product_id]['day_times_limit'] != 0) {
                $day = date('Y-m-d',time());
                $tmp_filter = $filter;
                $tmp_filter['product_id'] = $product_id;
                $tmp_filter['createtime|between'] = array($day.' 00:00:00',$day.' 23:59:59');
                if($mdl_member_log->count($filter) >= $restrict_product[$product_id]['day_times_limit']) {
                    $msg = '限购商品不能超出 用户每天限购次数';
                    return false;
                };
                //判断商品 用户每天限购数量
            }elseif($restrict_product[$product_id] && $restrict_product[$product_id]['day_member_limit'] != 0) {
                $day = date('Y-m-d',time());
                $tmp_filter = $filter;
                $tmp_filter['product_id'] = $product_id;
                $tmp_filter['createtime|between'] = array($day.' 00:00:00',$day.' 23:59:59');
                if(($mdl_member_log->sum_quantity($filter)+$goods['quantity']) > $restrict_product[$product_id]['day_member_limit']) {
                    $msg = '限购商品不能超出 用户每天限购数量';
                    return false;
                };
            }
            if($restrict_goods[$goods_id]['day_times_limit'] != 0 ) {
                $day = date('Y-m-d',time());
                $tmp_filter = $filter;
                $tmp_filter['goods_id'] = $goods_id;
                $tmp_filter['createtime|between'] = array($day.' 00:00:00',$day.' 23:59:59');
                if($mdl_member_log->count($tmp_filter) >= $restrict_goods[$goods_id]['day_times_limit']) {
                    $msg = '限购商品不能超出 用户每天限购次数';
                    return false;
                };
            }elseif($restrict_goods[$goods_id]['day_member_limit'] != 0 ) {
                $day = date('Y-m-d',time());
                $tmp_filter = $filter;
                $tmp_filter['goods_id'] = $goods_id;
                $tmp_filter['createtime|between'] = array($day.' 00:00:00',$day.' 23:59:59');
                if(($mdl_member_log->sum_quantity($tmp_filter)+$goods_sum) > $restrict_goods[$goods_id]['day_member_limit']) {
                    $msg = '限购商品不能超出 用户每天限购数量';
                    return false;
                };
            }

            //判断货品 用户购买数量是否超购
            if($restrict_product[$product_id] && $restrict_product[$product_id]['member_limit'] != 0) {
                $tmp_filter = $filter;
                $tmp_filter['product_id'] = $product_id;
                if(($mdl_member_log->sum_quantity($tmp_filter)+$goods['quantity']) > $restrict_product[$product_id]['member_limit']) {
                    $msg = '限购商品不能超额购买';
                    return false;
                };
            }
            if($restrict_goods[$goods_id]['member_limit'] != 0 ) {
                $tmp_filter = $filter;
                $tmp_filter['goods_id'] = $goods_id;
                if(($mdl_member_log->sum_quantity($tmp_filter)+$goods_sum) > $restrict_goods[$goods_id]['member_limit']) {
                    $msg = '限购商品不能超额购买';
                    return false;
                };
            }

            $filter = array(
                'res_id' => $res_id,
            );
            //判断货品 总量购买数量是否超购
            if($restrict_product[$product_id] && $restrict_product[$product_id]['sum'] != 0) {
                $tmp_filter = $filter;
                $tmp_filter['product_id'] = $product_id;
                if(($mdl_member_log->sum_quantity($tmp_filter)+$goods['quantity']) > $restrict_product[$product_id]['sum']) {
                    $msg = '限购商品不能超额购买';
                    return false;
                };
            }
            if($restrict_goods[$goods_id]['sum'] != 0) {
                $tmp_filter = $filter;
                $tmp_filter['goods_id'] = $goods_id;
                if(($mdl_member_log->sum_quantity($tmp_filter)+$goods_sum) > $restrict_goods[$goods_id]['sum']) {
                    $msg = '限购商品不能超额购买';
                    return false;
                };
            }

        }
        return true;
    }

    /*
     * 记录购买限购商品失败
     * @param 日志基本数据
     * @param 订单信息
     * @msg   错误信息
     * */
    public function save_log($log_data,$order,&$msg) {
        $mdl_member_log = app::get('restrict')->model('member_log');
        if($log_data) {
            foreach($log_data as $log) {
                $log['order_id'] = $order['order_id'];
                $log['createtime'] = $order['createtime'];
                if(!$mdl_member_log->save($log)){
                    $msg = '记录购买限购商品失败';
                    return false;
                };
            }
        };
        return true;
    }




}
