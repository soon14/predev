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


class preselling_order_refundfinish
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

    /**
     * 预售订单退款成功后的处理.
     * @params array 支付完的信息
     * @params 支付时候成功的信息
     */
    public function exec(&$bill, &$msg = '')
    {
        if ($bill['status'] != 'succ' && $bill['status'] != 'progress') {
            $msg = '支付其实没有完成!';

            return false;
        }
        $presell_id = $bill['order_id'];
        if (!$presell_id) {
            $msg = '未知预售订单ID';

            return false;
        }
        $mdl_orders = $this->app->model('orders');
        $order_sdf = $mdl_orders->dump($presell_id);
        if (!$order_sdf) {
            $msg = '未知预售订单';
            return false;
        }

        if($bill['bill_id'] == $order_sdf['deposit_refund_id']) {
            $update['deposit_pay_status'] = '3'; //全额退
            $update['status'] = '3';
        }elseif($bill['bill_id'] == $order_sdf['balance_refund_id']){
            $update['balance_pay_status'] = '3'; //全额退
            $update['status'] = '3';
        }
        if($update){
            if (!$mdl_orders->update($update, array('presell_id'=>$presell_id))) {
                $msg = '预售单据信息更新失败!';
                return false;
            }
        }
        return true;
    }

}