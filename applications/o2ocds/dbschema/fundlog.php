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
        'relation_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'ID',
            'comment' => 'ID',
        ),
        'relation_type' => array(
            'type' => array(
                'enterprise' => '企业',
                'store' => '店铺',
            ),
            'label' => '属性',
            'comment' => '分佣账号类型',
            'required' => true,
            'default' =>'enterprise',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'current_fund' => array(
            'type' => 'money',
            'required' => true,
            'default' => 0,
            'label' => '当前余额',
            'comment' => '当前余额',
        ),
        'change_fund' => array(
            'type' => 'money',
            'required' => true,
            'default' => 0,
            'label' => '变动资金',
            'comment' => '实际变动资金',
        ),
        'frozen_fund' => array(
            'type' => 'money',
            'required' => true,
            'default' => 0,
            'label' => '冻结资金',
            'comment' => '冻结资金',
        ),
        'type' => array(
            'type' =>
                array(
                    '1' => ('佣金冻结中'),
                    '2' => ('佣金已到账'),
                    '3' => ('订单已退款，佣金无效'),
                    '4' => ('申请提现,冻结中'),
                    '5' => ('提现成功，扣除'),
                    '6' => ('提现失败，返回'),
                ),
            'required' => true,
            'label' => '变动类型',
            'comment' => '变动类型',
        ),
        'opt_id' => array(
            'type' => 'number',
            'label' => '操作人员ID',
            'comment' => '操作人员ID',
        ),
        'opt_type' => array(
            'type' => array(
                'unknown' => ('未知身份'),
                'member' => ('普通会员'),
                'shopadmin' => ('管理员'),
                'seller' => ('供应商'),
                'system' => ('系统'),
            ),
            'default' => 'unknown',
            'required' => true,
            'label' => '操作人身份',
            'comment' => '操作人身份',
        ),
        'opt_time' => array(
            'type' => 'time',
            'required' => true,
            'label' => '操作时间',
            'comment' => '操作时间',
        ),
        'mark' => array(
            'type' => 'varchar(255)',
            'label' => '备注信息',
            'comment' => '备注信息',
        ),
        'extfield' => array(
            'type' => 'bigint unsigned',
            'comment' => '相关ID，附加字段',
            'in_list' => true,
        )
    ),
    'comment' => '用户佣金明细表',
    'index' => array(
        'ind_relation_id' => array(
            'columns' =>
                array(
                    0 => 'relation_id',
                ),
        )
    )
);
