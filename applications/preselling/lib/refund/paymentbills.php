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


class preselling_refund_paymentbills
{
    /**
     * 公开构造方法.
     *
     * @params app object
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /*
     * 查询出预售支付单号
     * */
    public function exec($order_sdf) {
        $order_id = $order_sdf['order_id'];
        if(!$order_id) {
            return false;
        }
        if(!$order = app::get('preselling')->model('orders')->getRow('presell_id,deposit_bill_id,deposit_refund_id,balance_bill_id,balance_refund_id',array('order_id'=>$order_id))) {
            return false;
        };
        $bill_ids = array();
        if($order['deposit_bill_id'] && !$order['deposit_refund_id']) {
            $bill_ids[] = $order['deposit_bill_id'];
        }
        if($order['balance_bill_id'] && !$order['balance_refund_id']) {
            $bill_ids[] = $order['balance_bill_id'];
        }
        if(!$bill_ids) {
            return false;
        }
        if(!$bills = app::get('ectools')->model('bills')->getList('*',array('bill_id'=>$bill_ids))) {
            return false;
        };
        return $bills;
    }

}