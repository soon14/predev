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


final class integralmall_payment_integraldeduction extends ectools_payment_parent implements ectools_payment_interface
{
    public $name = '积分账户支付';
    public $version = '1.0';
    public $intro = '会员积分余额全额支付订单';
    public $platform_allow = array(
        '_NONE_'
    );
    public function setting()
    {
        return array(
            'display_name' => array(
                'title' => '支付方式名称' ,
                'type' => 'hidden',
                'default' => '积分账户支付',
            ) ,
            'order_num' => array(
                'title' => '排序' ,
                'type' => 'hidden',
                'default' => 99,
            ) ,
            'pay_fee' => array(
                'title' => '交易费率 (%)' ,
                'type' => 'hidden',
                'default' => 0,
            ) ,
            'description' => array(
                'title' => '支付方式描述' ,
                'type' => 'hidden',
                'default' => '会员积分余额全额支付订单',
            ) ,
            'status' => array(
                'title' => '是否开启此支付方式' ,
                'type' => 'hidden',
                'default' => 'true',
            ) ,
        );
    }
    public function dopay($payment, &$msg)
    {
        return true;
    }
    public function callback(&$recv)
    {
    }
    public function notify(&$recv)
    {
    }
}
