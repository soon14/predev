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


$db['purchases_receipts'] = array(
    'columns' => array(
        'purchases_receipts_id'       => array(
            'type'    => 'number',
            'pkey'    => true,
            'extra'   => 'auto_increment',
            'comment' => ('进货单主键id'),
        ),
        'store_id'       => array(
            'type'    => 'table:store@store',
            'required' => true,
            'default'   => '0',
            'label' => '所属店铺',
            'comment' => ('店铺id'),
        ),
        'purchases_receipts_bn'       => array(
            'type'     => 'char(32)',
            'required' => true,
            'default'  => '',
            'label'    => '进货单编号',
            'comment'  => '进货单编号',
            'searchtype'      => 'has',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'purchases_sku_num'     => array(
            'type'            => 'int',
            'required'        => true,
            'default'         => '0',
            'label'           => 'SKU数',
            'comment'         => '进货商品sku数量',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'purchases_total_num'   => array(
            'type'            => 'int',
            'required'        => true,
            'default'         => '0',
            'label'           => '进货总数',
            'comment'         => '进货商品总数量',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'purchases_total_money' => array(
            'type'            => 'money',
            'required'        => true,
            'default'         => '0',
            'label'           => '货款合计',
            'comment'         => '进货商品总金额',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'purchases_date'        => array(
            'type'            => 'time',
            'required'        => true,
            'default'         => '0',
            'label'           => '进货日期',
            'comment'         => '进货日期',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'purchaser_name'     => array(
            'type'            => 'varchar(30)',
            'required'        => true,
            'default'         => '',
            'label'           => '进货人',
            'comment'         => '进货人姓名',
            'searchtype'      => 'has',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'purchaser_id'       => array(
            'type'            => 'number',
            'required'        => true,
            'default'         => '0',
            'label'           => '操作人',
            'comment'         => '进货人用户id',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'purchases_receipts_remark'   => array(
            'type'            => 'text',
            'label'           => '备注',
            'comment'         => '进货单备注',
            'in_list'         => true,
            'default_in_list' => true,
        ),
    ),
    'index'   => array(
        'uq_purchases_receipts_bn' => array(
            'columns' => array(
                0 => 'purchases_receipts_bn',
            ),
            'prefix'  => 'unique',
        ),
        'ind_purchaser_id' => array(
            'columns' => array(
                0 => 'purchaser_id',
            ),
        ),
    ),
    'engine'  => 'innodb',
    'comment' => ('进货单表'),
);
