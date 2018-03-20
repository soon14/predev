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
            'bill_id' => 'bill_id',
            'money' => 'money',
            'currency' => 'currency',
            'cur_rate' => 'cur_rate',
            'member_id' => 'member_id',
            'order_id' => 'order_id',
            'pay_mode' => 'pay_mode',
            'payee_account' => 'payee_account',
            'payee_bank' => 'payee_bank',
            'payer_account' => 'payer_account',
            'payer_bank' => 'payer_bank',
            'pay_app_id' => 'pay_app_id',
            'pay_fee' => 'pay_fee',
            'out_trade_no' => 'out_trade_no',
            'memo' => 'memo',
            'createtime' => 'createtime',
            'last_modify' => 'last_modify',
        ),
        'input' => array(
        ),
        'output' => array(
        ),
    ),
    // refundapply.read.queryPageList - 退款审核单列表查询 
    'read_queryPageList' => array(
        'fields' => array(
        ),
        'input' => array(
            'fields' => 'fields',
            'ids' => 'ids',
            'status' => 'status',
            'order_id' => 'order_id',
            'member_id' => 'member_id',
            'create_start_date' => 'create_start_date',
            'create_end_date' => 'create_end_date',
            'modify_start_date' => 'modify_start_date',
            'modify_end_date' => 'modify_end_date',
            'page' => 'page',
            'page_size' => 'page_size',
        ),
        'output' => array(
        ),
    ),
    // refundapply.read.queryById - 根据Id查询退款审核单 
    'read_queryById' => array(
        'fields' => array(
        ),
        'input' => array(
            'bill_id' => 'id',
            'fields' => 'fields',
        ),
        'output' => array(
        ),
    ),
    // refundapply.read.getWaitRefundNum - 待处理退款单数查询 
    'read_getWaitRefundNum' => array(
        'fields' => array(
            'total' => 'total_count',
        ),
        'input' => array(
        ),
        'output' => array(
        ),
    ),
    // refundapply.write.replyRefund - 审核退款单 
    'write_replyRefund' => array(
        'fields' => array(
            'modified' => 'modified',
            'bill_id' => 'bill_id',
        ),
        'input' => array(
            'bill_id' => 'bill_id',
            'order_id' => 'order_id',
            'pay_app_id' => 'pay_app_id',
            'payer_account' => 'payer_account',
            'payer_bank' => 'payer_bank',
            'out_trade_no' => 'out_trade_no',
            'memo' => 'memo',
        ),
        'output' => array(
        ),
    ),
);
