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
return array(
    // 默认
    '__' => array(
        'fields' => array(
        ),
        'output' => array(
        ),
    ),
    // biils.payment.succ - 订单支付完成 
    'payment_succ' => array(
        'fields' => array(
            'bill_type' => 'bill_type',
            'pay_object' => 'pay_object',
            'member_id' => 'member_id',
            'status' => 'status',
            'pay_mode' => 'pay_mode',
            'order_id' => 'order_id',
            'pay_app_id' => 'pay_app_id',
            'payee_account' => 'payee_account',
            'money' => 'money',
            'bill_id' => 'bill_id',
            'pay_fee' => 'pay_fee',
            'createtime' => 'createtime',
        ),
        'output' => array(
        ),
    ),
    // biils.payment.progress - 订单支付到担保方完成 
    'payment_progress' => array(
        'fields' => array(
            'bill_type' => 'bill_type',
            'pay_object' => 'pay_object',
            'member_id' => 'member_id',
            'status' => 'status',
            'pay_mode' => 'pay_mode',
            'order_id' => 'order_id',
            'pay_app_id' => 'pay_app_id',
            'payee_account' => 'payee_account',
            'money' => 'money',
            'bill_id' => 'bill_id',
            'pay_fee' => 'pay_fee',
            'createtime' => 'createtime',
        ),
        'output' => array(
        ),
    ),
    // biils.refund.succ - 订单退款完成 
    'refund_succ' => array(
        'fields' => array(
            'bill_type' => 'bill_type',
            'pay_object' => 'pay_object',
            'member_id' => 'member_id',
            'status' => 'status',
            'order_id' => 'order_id',
            'pay_app_id' => 'pay_app_id',
            'payee_bank' => 'payee_bank',
            'payee_account' => 'payee_account',
            'money' => 'money',
            'out_trade_no' => 'out_trade_no',
            'pay_fee' => 'pay_fee',
            'payer_bank' => 'payer_bank',
            'payer_account' => 'payer_account',
            'memo' => 'memo',
            'bill_id' => 'bill_id',
            'pay_mode' => 'pay_mode',
            'createtime' => 'createtime',
        ),
        'output' => array(
        ),
    ),
    // biils.refund.progress - 订单退款到担保方完成 
    'refund_progress' => array(
        'fields' => array(
            'bill_type' => 'bill_type',
            'pay_object' => 'pay_object',
            'member_id' => 'member_id',
            'status' => 'status',
            'order_id' => 'order_id',
            'pay_app_id' => 'pay_app_id',
            'payee_bank' => 'payee_bank',
            'payee_account' => 'payee_account',
            'money' => 'money',
            'out_trade_no' => 'out_trade_no',
            'pay_fee' => 'pay_fee',
            'payer_bank' => 'payer_bank',
            'payer_account' => 'payer_account',
            'memo' => 'memo',
            'bill_id' => 'bill_id',
            'pay_mode' => 'pay_mode',
            'createtime' => 'createtime',
        ),
        'output' => array(
        ),
    ),
);
