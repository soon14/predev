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


$db['fundlog'] = array(
    'columns' => array(
        'fundlog_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'ID',
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'member_id' => array(
            'type' => 'table:members@b2c',
            'required' => true,
            'label' => '会员帐号',
            'comment' => '会员id',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'current_fund' => array(
            'type' => 'decimal(20,3)',
            'required' => true,
            'default' => 0,
            'label' => '当前可用余额',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'change_fund' => array(
            'type' => 'decimal(20,3)',
            'required' => true,
            'default' => 0,
            'label' => '变动额',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'frozen_fund' => array(
            'type' => 'decimal(20,3)',
            'required' => true,
            'default' => 0,
            'label' => '冻结',
            'filtertype' => 'normal',
            'in_list' => true,
        ),
        'type' => array(
            'type' => array(
                    '1' => '充值',
                    '2' => '支付',
                    '3' => '退款',
                    '4' => '收益',
                    '5' => '冻结',
                    '6' => '提现',
                    '7' => '返还',
                    '8' => '批量充值',
                ),
            'required' => true,
            'label' => '变动类型',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'opt_id' => array(
            'type' => 'number',
            'label' => '操作人员ID',
            'comment' => '操作人员ID',
            'in_list' => true,
        ),
        'opt_type' => array(
            'type' => array(
                'unknown' => ('未知身份'),
                'member' => ('普通会员'),
                'shopadmin' => ('管理员'),
                'system' => ('系统'),
            ),
            'default' => 'unknown',
            'required' => true,
            'label' => '操作人身份',
            'comment' => '操作人身份',
            'in_list' => true,
        ),
        'opt_time' => array(
            'type' => 'time',
            'required' => true,
            'label' => '操作时间',
            'comment' => '操作时间',
            'in_list' => true,
        ),
        'mark' => array(
            'type' => 'varchar(255)',
            'label' => '备注信息',
            'comment' => '备注信息',
            'in_list' => true,
        ),
        'bill_id' => array(
            'type' => 'bigint unsigned',
            'label' => '交易单id',
            'comment' => '交易单id',
            'in_list' => true,
        ),
        'extfield' => array(
            'type' => 'varchar(50)',
            'label' => '附加数据',
            'comment' => '相关ID，附加字段，order_id',
            'in_list' => true,
        ),
    ),
    'comment' => '用户资金明细表',
);
