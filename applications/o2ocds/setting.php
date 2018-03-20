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
    /*'relation_change' => array(
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
    ),*/
    'trigger_type' => array(
        'required' => true,
        'type' => 'select',
        'options' => array(
            '1' => '订单发货时',
            /*'2' => '订单完成时',
            '3' => '订单付款完成时',*/
        ),
        'default' => '1',
        'desc' => '分佣触发节点',
    ),
    'enterprise_ratio' => array(
        'type' => 'text',
        'default' => '0',
        'desc' => '企业分佣基础比例',
        //'class' => 'mode mode-1',
    ),
    'store_ratio' => array(
        'type' => 'text',
        'default' => '0',
        'desc' => '店铺分佣基础比例',
        //'class' => 'mode mode-1',
    ),
    'min_cash' => array(
        'type' => 'text',
        'default' => '100.00',
        'required' => true,
        'desc' => '最低结算金额',
        'helpinfo' => '单位：元',
    ),
    'auto_statement_day' => array(
        'type' => 'text',
        'default' => '7',
        'required' => true,
        'desc' => '自动生成结算单时间',
        'helpinfo' => '<span class="text-danger">单位：天 （更改结算时间将在下一周期生效）</span>'
    ),
    'o2ocds_rule' => array(
        'type' => 'html',
        'default' => '分佣规则说明',
        'desc' => '分佣规则说明',
    ),
    'cash_rule' => array(
        'type' => 'html',
        'default' => '提现规则说明',
        'desc' => '提现规则说明',
    ),
);
