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


$db['income_receipts'] = array(
    'columns' => array(
        'income_receipts_id'       => array(
            'type'    => 'number',
            'pkey'    => true,
            'extra'   => 'auto_increment',
            'comment' => ('损益单主键id'),
        ),
        'store_id'       => array(
            'type'    => 'table:store@store',
            'required' => true,
            'default'   => '0',
            'label' => '所属店铺',
            'comment' => ('店铺id'),
        ),
        'income_receipts_bn'       => array(
            'type'     => 'char(32)',
            'required' => true,
            'default'  => '',
            'label'    => '损益单编号',
            'comment'  => '损益单编号',
            'searchtype'      => 'has',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'check_profit_num' => array(
            'type'            => 'int',
            'required'        => true,
            'default'         => '0',
            'label'           => '盘盈货数',
            'comment'         => '盘盈货数',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'check_profit_money' => array(
            'type'            => 'money',
            'required'        => true,
            'default'         => '0',
            'label'           => '盘盈金额',
            'comment'         => '盘盈金额',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'check_loss_num' => array(
            'type'            => 'int',
            'required'        => true,
            'default'         => '0',
            'label'           => '盘损货数',
            'comment'         => '盘损货数',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'check_loss_money' => array(
            'type'            => 'money',
            'required'        => true,
            'default'         => '0',
            'label'           => '盘损金额',
            'comment'         => '盘损金额',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'income_date'        => array(
            'type'            => 'time',
            'required'        => true,
            'default'         => '0',
            'label'           => '盘点日期',
            'comment'         => '盘点日期',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'incomer_name'     => array(
            'type'            => 'varchar(30)',
            'required'        => true,
            'default'         => '',
            'label'           => '盘点人',
            'comment'         => '盘点人姓名',
            'searchtype'      => 'has',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'incomer_id'       => array(
            'type'            => 'number',
            'required'        => true,
            'default'         => '0',
            'label'           => '操作人',
            'comment'         => '操作员用户id',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'income_receipts_remark'   => array(
            'type'            => 'text',
            'label'           => '备注',
            'comment'         => '损益单备注',
            'in_list'         => true,
            'default_in_list' => true,
        ),
    ),
    'index'   => array(
        'uq_income_receipts_bn' => array(
            'columns' => array(
                0 => 'income_receipts_bn',
            ),
            'prefix'  => 'unique',
        ),
        'ind_incomer_id' => array(
            'columns' => array(
                0 => 'incomer_id',
            ),
        ),
    ),
    'engine'  => 'innodb',
    'version' => '1.0',
    'comment' => ('损益单表'),
);
