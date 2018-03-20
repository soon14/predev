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


class integraldeduction_order_cancelfinish
{
    public function exec(&$order_sdf, &$msg = '')
    {
        $integral_charge = array(
            'member_id' => $order_sdf['member_id'],
            'change_reason' => 'recharge',//退回抵扣积分
            'order_id' => $order_sdf['order_id'],
            'change' => $order_sdf['score_u'],
            'op_model' => 'member',
            'op_id' => $order_sdf['member_id'],
            'remark' => '订单作废积分抵扣额度退回',
        );
        if (!vmc::singleton('b2c_member_integral')->change($integral_charge,$error_msg)) {
            $msg = '积分抵扣退回失败!';
            logger::warning('积分抵扣退回失败.ORDER_ID:'.$order_sdf['order_id']);

            return false;
        } else {
            return true;
        }
    }
}
