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


class preselling_checkout_stage
{
    //$params  member_id,addr_id,dlytype_id,payapp_id，payapp_filter,quantity
    public function check($params)
    {
        $order = $params['order'];
        $member_id = $order['member_id'];
        $addr_id = $params['addr_id'];
        $dlytype_id = $params['dlytype_id'];
        $payapp_id = $params['payapp_id'];
        $activity = $params['activity'];

        $_return = array();
        $last_checkout = $this->get_default($member_id); //会员最后一次购物时选择的配送和支付方式
        //会员收货地址
        $mdl_maddr = app::get('b2c')->model('member_addrs');
        if ($member_addrs = $mdl_maddr->getList('*', array(
            'member_id' => $member_id,
        ), 0, -1, '`is_default` ASC,updatetime DESC,`addr_id`')) {
            $def_addr = $member_addrs[0]; //会员默认收货地址
            $member_addrs = utils::array_change_key($member_addrs, 'addr_id');

            if ($addr_id) {
                $member_addrs[$addr_id]['selected'] = 'true';
                $area = $member_addrs[$addr_id]['area'];
            } else {
                $area = $def_addr['area'];
                $member_addrs[$def_addr['addr_id']]['selected'] = 'true';
            }
            $area_id = array_pop(explode(':', $area));
            $_return['member_addrs'] = $member_addrs;
        }
        if ($params['is_delivery'] != 'N') {
            //根据地区获得送货方式
            $mdl_dltype = app::get('b2c')->model('dlytype');
            $dlytypes = $mdl_dltype->getAvailable($area_id);
            $dlytypes = utils::array_change_key($dlytypes, 'dt_id');
            foreach($dlytypes as $k=>$dlytype) {
                if($dlytype['has_cod'] == 'true') {
                    unset($dlytypes[$k]);
                }
            }
            $dlytype_id = ($dlytype_id ? $dlytype_id : $last_checkout['dlytype_id']);
            if ($dlytypes[$dlytype_id]) {
                $dlytypes[$dlytype_id]['selected'] = 'true';
                if ($dlytypes[$dlytype_id]['has_cod'] == 'true') {
                    $is_cod = true; //为了货到付款锁定在线支付
                }
            } else {
                $dlytypes[key($dlytypes) ]['selected'] = 'true';
            }
            $_return['dlytypes'] = $dlytypes;
        }
        //支付方式
        $payapp_filter = array(
            'status' => 'true',
            'platform_allow' => array(
                'pc',
            ),
        );
        if ($params['payapp_filter']) {
            $payapp_filter = array_merge($payapp_filter, $params['payapp_filter']);
        }
        if(base_component_request::is_wxapp()){
            $payapp_filter['platform_allow'] =  array(
                'wxapp', //微信小程序
            );
        }elseif(base_mobiledetect::is_hybirdapp()){
            $payapp_filter['platform_allow'] =  array(
                'app', //hybirdapp
            );
        }elseif(base_mobiledetect::is_mobile()){
            $payapp_filter['platform_allow'] =  array(
                'mobile', //H5
            );
        }

        if ($is_cod) {
            unset($payapp_filter['platform_allow']);
            $payapp_filter['app_id'] = 'cod';
        }
        $mdl_payapps = app::get('ectools')->model('payment_applications');
        $paymentapps = $mdl_payapps->getList('*', $payapp_filter);
        $paymentapps = utils::array_change_key($paymentapps, 'app_id');
        $payapp_id = ($payapp_id ? $payapp_id : $last_checkout['pay_app']);
        if ($paymentapps[$payapp_id]) {
            $paymentapps[$payapp_id]['selected'] = 'true';
        } else {
            $paymentapps[key($paymentapps) ]['selected'] = 'true';
        }
        $_return['paymentapps'] = $paymentapps;

        $order_sdf_tmp = array(
            'consignee' => array(
                'area' => $area,
            ) ,
            'dlytype_id' => $dlytype_id,
        );
        if ($params['is_delivery'] == 'N') {
            //无需运输
            unset($order_sdf_tmp['dlytype_id']);
        }

        $obj_math = vmc::singleton('ectools_math');
        $activity_result['order_total'] = $obj_math->number_plus(array($order['balance_payment'],$order['cost_freight']));
        $activity_result['cart_md5'] = utils::encrypt(array(
            'presell_id'=>$order['presell_id']
        ));
        $_return['activity'] = $activity;
        $_return['order'] = $order;
        $_return['activity_result'] = $activity_result;
        return $_return;
    }

    public function check_presell($params) {
        $member_id = $params['member_id'];
        $payapp_id = $params['payapp_id'];
        $activity = $params['activity'];
        $quantity = $params['quantity'];

        $_return = array();
        $last_checkout = $this->get_default($member_id); //会员最后一次购物时选择的配送和支付方式
        //支付方式
        $payapp_filter = array(
            'status' => 'true',
            'platform_allow' => array(
                'pc',
            ),
        );
        if ($params['payapp_filter']) {
            $payapp_filter = array_merge($payapp_filter, $params['payapp_filter']);
        }
        if(base_component_request::is_wxapp()){
            $payapp_filter['platform_allow'] =  array(
                'wxapp', //微信小程序
            );
        }elseif(base_mobiledetect::is_hybirdapp()){
            $payapp_filter['platform_allow'] =  array(
                'app', //hybirdapp
            );
        }elseif(base_mobiledetect::is_mobile()){
            $payapp_filter['platform_allow'] =  array(
                'mobile', //H5
            );
        }
        $mdl_payapps = app::get('ectools')->model('payment_applications');
        $paymentapps = $mdl_payapps->getList('*', $payapp_filter);
        $paymentapps = utils::array_change_key($paymentapps, 'app_id');
        $payapp_id = ($payapp_id ? $payapp_id : $last_checkout['pay_app']);
        if ($paymentapps[$payapp_id]) {
            $paymentapps[$payapp_id]['selected'] = 'true';
        } else {
            $paymentapps[key($paymentapps) ]['selected'] = 'true';
        }
        $_return['paymentapps'] = $paymentapps;

        $obj_math = vmc::singleton('ectools_math');
        $activity_result['product'] = app::get('b2c')->model('products')->getRow('*',array('product_id'=>$params['product_id']));
        $activity_result = array_merge($activity_result,$activity['conditions'][$params['product_id']]);
        $activity_result['order_total'] = $obj_math->number_multiple(array($quantity,$activity_result['deposit']));
        $activity_result['quantity'] = $quantity;
        $activity_result['cart_md5'] = utils::encrypt(array(
            'product_id'=>$params['product_id'],
            'activity_id'=>$activity['activity_id'],
        ));
        $_return['activity'] = $activity;
        $_return['activity_result'] = $activity_result;
        return $_return;
    }

    public function changepayment($id, $new_payappid, &$msg)
    {
        $mdl_orders = app::get('preselling')->model('orders');
        //TODO 支付费率计算，费率记录，订单总价变更
        $order = $mdl_orders->getRow('pay_status,payed,pay_app,cost_freight,order_total', array('presell_id' => $id));
        $opayappid = $order['pay_app'];//原支付方式ID
        if ($order['pay_app'] == $new_payappid) {
            return true;
        }
        /*if ($order['pay_status'] != '0' || $order['payed'] > 0) {
            $msg = '订单已进行支付操作,无法修改支付方式.';

            return false;
        }*/
        $mdl_payapps = app::get('ectools')->model('payment_applications');
        $payapp = $mdl_payapps->dump($new_payappid);
        if (!$payapp) {
            $msg = '未知的支付方式.';

            return false;
        }
        if (!$mdl_orders->update(array('pay_app' => $new_payappid), array('presell_id' => $id))) {
            $msg = '支付方式修改失败.';

            return false;
        }
        logger::info('订单支付方式变更.ORDERID:'.$id.',原支付方式ID:'.$opayappid.',新支付方式ID:'.$new_payappid);

        return true;
    }

    /*得到会员最后一次订单确认习惯数据*/
    private function get_default($member_id)
    {
        $mdl_orders = app::get('b2c')->model('orders');
        $last_order = $mdl_orders->getRow('pay_app,dlytype_id,member_id', array(
            'member_id' => $member_id,
        ), 'createtime DESC');
        if (!empty($last_order)) {
            return array(
                'pay_app' => $last_order['pay_app'],
                'dlytype_id' => $last_order['dlytype_id'],
            );
        }

        return false;
    }
}
