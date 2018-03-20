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


$db['member_relation'] = array(
    'columns' => array(
        'member_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'label' => '用户',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'commission_id' => array(
            'type' => 'varchar(10)',
            'label' => '分佣标示',
            'comment' => '分佣标示',
            'in_list' => true,
        ),
        'parent_id' => array(
            'type' => 'mediumint(8)',
            'label' => '直属上级ID',
            'comment' => '直属上级ID',
            'required' => true,
            'default' =>0,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'parent_path' => array(
            'type' => 'varchar(50)',
            'label' => '上级关系',
            'comment' => '上级关系（上级，上上级）',
            'required' => true,
            'default' =>'',
        ),
        'parents' => array(
            'type' => 'varchar(100)',
            'label' => '上级关系',
            'comment' => '上级关系（上级，上上级...）',
            'required' => true,
            'default' =>'',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'used_fund' => array(
            'type' => 'money',
            'required' => true,
            'default' => 0,
            'label' => '可用资金',
            'comment' => '可用资金',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'frozen_fund' => array(
            'type' => 'money',
            'required' => true,
            'default' => 0,
            'label' => '冻结资金',
            'comment' => '冻结资金',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'coin' => array(
            'type' => 'money',
            'required' => true,
            'default' => 0,
            'label' => '闪币',
            'comment' => '闪币',

        ),
        'domain_pre' => array(
            'type' => 'varchar(20)',
            'label' => '二级域名',
            'comment' => '手动添加二级域名',
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
            'label' => '银行类型',
            'comment' => '银行类型',
        ),
        'bank_account' => array(
            'type' => 'varchar(30)',
            'label' => '银行账号',
            'comment' => '银行账号',
        ),
        'account_name' => array(
            'type' => 'varchar(20)',
            'label' => '开户姓名',
            'comment' => '开户姓名',
        ),
        'is_commission' => array(
            'type' =>
                array(
                    '0' => ('否'),
                    '1' => ('是'),
                ),
            'default' => '0',
            'comment' => '是否成为分佣者',
            'label' => '是否为分佣者',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'relation_change' => array(
            'type' =>
                array(
                    '1' => ('跟随会员等级升迁'),
                    '2' => ('不跟随会员等级升迁'),
                ),
            'default' => '1',
            'comment' => '是否跟随会员等级升迁',
            'required' => true,
        ),

    ),
    'comment' => '用户关系信息数据表',
    'index' => array(
        'ind_domain_pre' => array(
            'columns' =>
                array(
                    0 => 'domain_pre',
                ),
        ),
        'ind_commission_id' => array(
            'columns' =>
                array(
                    0 => 'commission_id',
                ),
        ),
        'ind_parent_id' => array(
            'columns' =>
                array(
                    0 => 'parent_id',
                ),
        ),
    )
);
