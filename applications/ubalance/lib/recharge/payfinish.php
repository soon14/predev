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


class ubalance_recharge_payfinish
{

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 充值支付后的处理
     */
    public function exec(&$bill, &$msg = '')
    {
        $bill_id = $bill['bill_id'];
        $bill = app::get('ectools')->model('bills')->getRow('*', array('bill_id' => $bill_id));
        if ($bill['status'] != 'succ' && $bill['status'] != 'progress') {
            $msg = '支付其实没有完成!';

            return false;
        }
        if ($bill['bill_type'] != 'payment' || $bill['pay_object'] != 'recharge') {
            logger::error('支付单类型错,bill_id:' . $bill_id);

            return true;
        }

        $log = $this ->app ->model('fundlog') ->getRow('*' ,array('bill_id' =>$bill_id ,'type' =>'1'));
        if($log){
            logger::error('支付记录重复,bill_id:' . $bill_id);

            return true;
        }

        $log_data = array(
            'member_id' => $bill['member_id'],
            'change_fund' => $bill['money']*app::get('ubalance')->getConf('exchange_ratio'),
            'frozen_fund' => 0,
            'type' => $bill['op_id']?'8':'1',
            'opt_id' => $bill['op_id']?$bill['op_id']:$bill['member_id'],
            'opt_type' => $bill['op_id']?'shopadmin':'member',
            'opt_time' => time(),
            'mark' => $bill['op_id']?$bill['memo']:'用户充值',
            'bill_id' => $bill_id
        );
        if (!vmc::singleton('ubalance_account')->fund_change($log_data, $msg)) {
            logger::error('余额宝充值回调处理失败,bill_id:' . $bill_id . 'msg:' . $msg);

            return false;
        }

        return true;
    }
}
