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
$db['account'] = array(
    'columns' => array(
            'member_id' => array(
                    'pkey' => true,
                    'type' => 'table:members@b2c',
                    'required' => true,
                    'label' => ('用户ID'),
                    'comment' => '用户ID',
                ),
            'ubalance' => array(
                    'type' => 'decimal(20,3)',
                    'default' => 0,
                    'label' => ('可用额度'),
                    'filtertype' => 'yes',
                    'in_list' => true,
                    'default_in_list' => true,
                    'comment' => '可用额度',
                ),
            'frozen' => array(
                'type' => 'decimal(20,3)',
                'default' => 0,
                'label' => ('冻结额度'),
                'in_list' => true,
                'comment' => '冻结额度',
            ),
            'amount' => array(
                    'type' => 'decimal(20,3)',
                    'default' => 0,
                    'label' => ('充值总额'),
                    'filtertype' => 'yes',
                    'in_list' => true,
                    'default_in_list' => true,
                    'comment' => '充值总额',
                ),
            'frequency' => array(
                    'type' => 'number',
                    'default' => 0,
                    'label' => ('充值次数'),
                    'in_list' => true,
                    'default_in_list' => true,
                    'comment' => '充值次数',
                ),
            'income' => array(
                    'type' => 'decimal(20,3)',
                    'default' => 0,
                    'label' => ('累计收益'),
                    'in_list' => true,
                    'default_in_list' => true,
                    'comment' => '累计收益',
                ),
            'pay_password' => array(
                    'type' => 'password',
                    'label' => ('支付密码'),
                    'comment' => '支付密码',
                ),
            'last_modify' => array(
                    'type' => 'time',
                    'label' => ('最后更新时间'),
                    'in_list' => true,
                    'default_in_list' => true,
                    'comment' => '最后更新时间',
                ),
            'status' => array(
                    'type' => array(
                        '0' => '冻结',
                        '1' => '有效',
                    ),
                    'required' => true,
                    'default' => '1',
                    'label' => ('账户状态'),
                    'filtertype' => 'yes',
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            'remarks' => array(
                    'type' => 'varchar(30)',
                    'label' => ('备注'),
                    'in_list' => true,
                ),
        ),
    'comment' => ('余额宝账号表'),
);
