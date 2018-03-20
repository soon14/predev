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


class preselling_order_readyfinish
{
    /**
     * 公开构造方法.
     *
     * @params app object
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->omath = vmc::singleton('ectools_math');
    }

    /**
     * 退款单创建，成功的处理.
     * @params array 退款单的信息
     * @params 退款时候成功的信息
     */
    public function exec(&$bill, &$msg = '')
    {
        $mdl_orders = app::get('preselling')->model('orders');
        $order = $mdl_orders->getRow('*',array('presell_id'=>$bill['order_id']));
        if($bill['payment_bill_id'] == $order['deposit_bill_id']) {
            $order['deposit_pay_status'] = '2';
            $order['deposit_refund_id'] = $bill['bill_id'];
        }elseif($bill['payment_bill_id'] == $order['balance_bill_id']) {
            $order['balance_pay_status'] = '2';
            $order['balance_refund_id'] = $bill['bill_id'];
        }
        if(!$mdl_orders->save($order)) {
            $msg = '预售单更新信息失败';
            return false;
        };
        return true;
    }

}