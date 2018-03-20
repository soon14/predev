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

$db['cash'] = array(
    'columns' => array(
        'cash_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'ID',
            'pkey' => true,
            'extra' => 'auto_increment',
            'in_list' => true,
        ),
        'member_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '用户ID',
            'comment' => '用户ID',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'apply_fund' => array(
            'type' => 'money',
            'label' => '提取金额',
            'comment' => '提取金额',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'bank_type' => array(
            'type' =>
                array(
                    'alipay' => ('支付宝'),
                    'BKCHCNBJ' => ('中国银行'),
                    'ICBKCNBJ' => ('工商银行'),
                    'PCBCCNBJ' => ('建设银行'),
                    'ABOCCNBJ' => ('农业银行'),
                    'CMBCCNBS' => ('招商银行'),
                    'COMMCN' => ('交通银行'),
                    'CIBKCNBJ' => ('中信银行'),
                ),
            'required' => true,
            'label' => '银行类型',
            'comment' => '银行类型',
        ),
        'bank_account' => array(
            'type' => 'varchar(30)',
            'required' => true,
            'label' => '银行账号',
            'comment' => '银行账号',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'account_name' => array(
            'type' => 'varchar(20)',
            'required' => true,
            'label' => '开户姓名',
            'comment' => '开户姓名',
        ),
        'status' => array(
            'type' =>
                array(
                    '1' => ('等待处理'),
                    '2' => ('提现成功'),
                    '3' => ('提现失败'),
                ),
            'required' => true,
            'default' => '1',
            'label' => '提现状态',
            'comment' => '提现状态',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'mark' => array(
            'type' => 'varchar(200)',
            'label' => '备注信息',
            'comment' => '备注信息',
        ),
        'createtime' => array(
            'type' => 'time',
            'required' => true,
            'label' => '操作时间',
            'comment' => '操作时间',
        ),
    ),
    'index' => array(
        'ind_member_id' => array(
            'columns' => array(
                0 => 'member_id',
            ) ,
        ),
    ) ,
    'comment' => '用户申请提现表',
);
