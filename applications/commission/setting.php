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
$setting = array(
    'relation_change' => array(
        'required' => true,
        'type' => 'select',
        'options' => array(
            '1' => '跟随会员等级升迁',
            '2' => '不跟随会员等级升迁',
        ),
        'default' => '1',
        'desc' => '分佣关系',
        'helpinfo' => '请勿随意变更',
    ),
    'mode' => array(
        'required' => true,
        'type' => 'select',
        'options' => array(
            '1' => '不同分佣层级获取不同佣金，与会员等级无关',
            '2' => '不同会员等级获取不同佣金，与分佣层级无关',
        ),
        'default' => '1',
        'desc' => '分佣模式',
    ),
    'first_ratio' => array(
        'type' => 'text',
        'default' => '0',
        'desc' => '一级分佣基础比例',
        'class' => 'mode mode-1',
    ),
    'second_ratio' => array(
        'type' => 'text',
        'default' => '0',
        'desc' => '二级分佣基础比例',
        'class' => 'mode mode-1',
    ),
    'min_cash' => array(
        'type' => 'text',
        'default' => '100.00',
        'required' => true,
        'desc' => '最低提现金额',
        'helpinfo' => '单位：元',
    ),
    'last_cash_time' => array(
        'type' => 'day',
        'default' => date('10 23:59:59'),
        'required' => true,
        'desc' => '每月最晚提现时间',
    ),
    'commission_rule' => array(
        'type' => 'textarea',
        'default' => '分佣规则说明',
        'desc' => '分佣规则说明',
    ),
    'cash_rule' => array(
        'type' => 'textarea',
        'default' => '提现规则说明',
        'desc' => '提现规则说明',
    ),
);
