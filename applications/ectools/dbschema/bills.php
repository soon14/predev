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


$db['bills'] = array(
    'columns' => array(
        'bill_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'default' => 0,
            'pkey' => true,
            'label' => '账单流水号' ,
            'searchtype' => 'nequal',
            'in_list' => true,
            'default_in_list' => true,
            'is_title' => true,
        ) ,
        'bill_type' => array(
            'type' => array(
                'payment' => '付款' ,
                'refund' => '退款' ,
            ) ,
            'default' => 'payment',
            'required' => true,
            'label' => '单据类型' ,
            'in_list' => true,
            'comment' => '单据类型' ,
        ) ,
        'app_id' => array(
            'type' => 'varchar(32)' ,
            'default' => 'b2c',
            'required' => true,
            'label' => '应用id' ,
            'in_list' => true,
        ) ,
        'pay_object' => array(
            'type' => array(
                'order' => '订单' ,
                'recharge' => '充值',
                'cashout' => '提现',
                'gborder' => '拼团订单' ,
                'porder' => '预售订单' ,
            ) ,
            'default' => 'order',
            'required' => true,
            'label' => '业务对象' ,
            'in_list' => true,
        ) ,
        'money' => array(
            'type' => 'money',
            'label' => '支付货币金额' ,
            'default' => '0',
            'required' => true,
            'filtertype' => 'yes',
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
        ) ,
        'currency' => array(
            'type' => 'varchar(10)',
            'label' => '货币' ,
            'default' => 'CNY',
            'required' => true,
        ) ,
        'cur_rate' => array(
            'type' => 'decimal(10,4)',
            'default' => '1.0000',
            'comment' => '货币汇率' ,
        ) ,
        'op_id' => array(
            'type' => 'number', //'table:users@desktop',
            'label' => '相关操作员' ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'member_id' => array(
            'type' => 'varchar(100)',
            'label' => '相关会员' ,
            'in_list' => true,
        ) ,
        'order_id' => array(
            'type' => 'bigint unsigned',
            'default' => 0,
            'label' => '相关订单' ,
            'searchtype' => 'nequal',
            'in_list' => true,
        ),
        'status' => array(
            'type' => array(
                'succ' => '支付成功' ,
                'failed' => '支付失败' ,
                'cancel' => '未支付' ,
                'error' => '处理异常' ,
                'process' => '处理中',
                'invalid' => '非法参数' ,
                'progress' => '已付款至担保方' ,
                'timeout' => '超时' ,
                'ready' => '准备中' ,
                'dead' => '作废',
            ) ,
            'default' => 'ready',
            'orderby' => true,
            'required' => true,
            'label' => '支付状态' ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'pay_mode' => array(
            'type' => array(
                'online' => '在线支付' ,
                'offline' => '线下支付' ,
                'deposit' => '预存款支付',
            ) ,
            'default' => 'online',
            'required' => true,
            'label' => '付款方式' ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'payee_name' => array(
            'type' => 'varchar(50)',
            'label' => '收款者名称' ,
            'filtertype' => 'yes',
            'in_list' => true,
        ) ,
        'payee_account' => array(
            'type' => 'varchar(50)',
            'label' => '收款者账号' ,
            'filtertype' => 'yes',
            'in_list' => true,
        ) ,
        'payee_bank' => array(
            'type' => 'varchar(50)',
            'label' => '收款者银行' ,
            'filtertype' => 'yes',
            'in_list' => true,
        ) ,
        'payer_name' => array(
            'type' => 'varchar(50)',
            'label' => '付款者名称' ,
            'filtertype' => 'yes',
            'in_list' => true,
        ) ,
        'payer_account' => array(
            'type' => 'varchar(50)',
            'label' => '付款者账户' ,
            'filtertype' => 'yes',
            'in_list' => true,
        ) ,
        'payer_bank' => array(
            'type' => 'varchar(50)',
            'label' => '付款者银行' ,
            'filtertype' => 'yes',
            'in_list' => true,
        ) ,
        'pay_app_id' => array(
            'type' => 'varchar(100)',
            'label' => '支付应用程序' ,
            'required' => true,
            'default' => 0,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'pay_fee' => array(
            'type' => 'money',
            'label' => '手续费' ,
            'in_list' => true,
        ) ,
            // 'pay_app_ver' => array(
            //     'type' => 'varchar(50)',
            //     'label' => '支付版本号' ,
            //     'in_list' => false,
            // ) ,
        'ip' => array(
            'type' => 'ipaddr',
            'label' => '单据发起者IP' ,
            'in_list' => true,
        ) ,
        'return_url' => array(
            'type' => 'varchar(255)',
            'label' => '支付网关回调网址' ,
        ) ,
        'out_trade_no' => array(
            'type' => 'varchar(50)',
            'label' => '支付平台流水号' ,
            'searchtype' => 'nequal',
            'in_list' => true,
        ) ,
        'transaction_id' => array(
            'type' => 'varchar(50)',
            'label' => '相关支付平台流水号' ,
            'searchtype' => 'nequal',
            'in_list' => true,
        ) ,
        'auto_refund' => array(
            'type' => 'bool',
            'default' => 'false',
            'label' => '是否原路退回' ,
            'in_list' => true,
        ) ,
        'memo' => array(
            'type' => 'longtext',
            'label' => '备注' ,
            'searchtype' => 'has',
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => '支付单创建时间' ,
            'filtertype' => 'yes',
            'in_list' => true,
            'orderby' => true,
        ) ,
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => '最后更新时间' ,
            'filtertype' => 'yes',
            'orderby' => true,
            'in_list' => true,
        ) ,
        'disabled' => array(
            'type' => 'bool',
            'label' => '是否是禁用的单据' ,
            'default' => 'false',
        ) ,
    ) ,
    'engine' => 'innodb',
    'comment' => '钱款账单表' ,
);
