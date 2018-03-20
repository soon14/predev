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

class ubalance_order_refundfinish
{
    public function exec(&$bill, &$msg = '')
    {
        $order_id = $bill['order_id'];
        if (!$order_id) {
            $msg = '未知订单ID';
            return false;
        }
        if ($bill['pay_app_id'] != 'balance') {
            $msg = '不是余额宝支付方式';
            return true;
        }
        $omath = vmc::singleton('ectools_math');
        $change_fund = $omath->number_multiple(array($bill['money'], app::get('ubalance')->getConf('exchange_ratio', 1)));
        $log_data = array(
            'member_id' => $bill['member_id'],
            'change_fund' => $change_fund,
            'frozen_fund' => 0,
            'type' => '3',
            'opt_id' => 0,
            'opt_type' => 'system',
            'opt_time' => time(),
            'mark' => '退款操作,相关订单：'.$bill['order_id'],
            'bill_id' => $bill['bill_id'],
            'extfield' => $bill['order_id'],
        );
        if (!vmc::singleton('ubalance_account')->fund_change($log_data, $msg)) {
            logger::error('订单退款至余额宝处理失败,bill_id:' . $bill['bill_id'] . 'msg:' . $msg);
            return false;
        }
        return true;
    }
}
