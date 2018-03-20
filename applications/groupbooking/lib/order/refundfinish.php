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


class groupbooking_order_refundfinish
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
     * 拼团订单退款成功后的处理.
     * @params array 支付完的信息
     * @params 支付时候成功的信息
     */
    public function exec(&$bill, &$msg = '')
    {
        if ($bill['status'] != 'succ' && $bill['status'] != 'progress') {
            $msg = '支付其实没有完成!';

            return false;
        }
        $gb_id = $bill['order_id'];
        if (!$gb_id) {
            $msg = '未知拼团订单ID';

            return false;
        }
        $omath = vmc::singleton('ectools_math');
        $mdl_orders = $this->app->model('orders');
        $order_sdf = $mdl_orders->dump($gb_id);
        if (!$order_sdf) {
            $msg = '未知拼团订单';
            return false;
        }

        $exist_refund_bill = app::get('ectools')->model('bills')->getList('*', array('bill_type' => 'refund', 'order_id' => $gb_id,'status'=>'succ'));
        $sum_refund = 0;
        foreach ($exist_refund_bill as $rbill) {
            $sum_refund = $omath->number_plus(array(
                $sum_refund,
                $rbill['money'],
            ));
        }
        if ($sum_refund >= $order_sdf['payed']) {
            $update['pay_status'] = '5'; //全额退
        } else {
            $update['pay_status'] = '4'; //部分退
        }
        if (!$mdl_orders->update($update, array('gb_id'=>$gb_id))) {
            $msg = '订单主单据信息更新失败!';

            return false;
        }

        return true;

    }

}