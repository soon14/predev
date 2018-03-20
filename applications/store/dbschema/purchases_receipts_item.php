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


$db['purchases_receipts_item'] = array(
    'columns' => array(
        'purchases_receipts_item_id'        => array(
            'type'    => 'number',
            'pkey'    => true,
            'extra'   => 'auto_increment',
            'comment' => ('进货单详单主键id'),
        ),
        'purchases_receipts_bn'             => array(
            'type'     => 'char(32)',
            'required' => true,
            'default'  => '',
            'label'    => '进货单编号',
            'comment'  => '进货单编号',
        ),
        'goods_id'                 => array(
            'type'     => 'bigint unsigned',
            'required' => true,
            'default'  => '0',
            'comment'  => '商品id',
        ),
        'product_id'               => array(
            'type'     => 'bigint unsigned',
            'required' => true,
            'default'  => '0',
            'comment'  => '货品id',
        ),
        'goods_name'               => array(
            'type'            => 'varchar(150)',
            'required'        => true,
            'default'         => '',
            'label'           => '商品名称',
            'comment'         => '商品名称',
            'searchtype'      => 'has',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'goods_spec'              => array(
            'type'            => 'varchar(150)',
            'required'        => true,
            'default'         => '',
            'label'           => '商品规格',
            'comment'         => '商品规格',
            'searchtype'      => 'has',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'goods_bn'             => array(
            'type'            => 'varchar(150)',
            'required'        => true,
            'default'         => '',
            'label'           => '商品货号',
            'comment'         => '商品货号',
            'searchtype'      => 'has',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'goods_barcode'             => array(
            'type'            => 'varchar(150)',
            'required'        => true,
            'default'         => '',
            'label'           => '商品条码',
            'comment'         => '商品条码',
            'searchtype'      => 'has',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'goods_num'                => array(
            'type'            => 'int',
            'required'        => true,
            'default'         => '0',
            'label'           => '商品数量',
            'comment'         => '商品数量',
            'searchtype'      => 'has',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'purchases_goods_money'       => array(
            'type'            => 'money',
            'required'        => true,
            'default'         => '0',
            'label'           => '商品单价',
            'comment'         => '进货商品单价',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'purchases_goods_total_money' => array(
            'type'            => 'money',
            'required'        => true,
            'default'         => '0',
            'label'           => '商品总价',
            'comment'         => '进货商品总价',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'purchases_remark'   => array(
            'type'            => 'text',
            'label'           => '备注',
            'comment'         => '进货备注',
            'in_list'         => true,
            'default_in_list' => true,
        ),
    ),
    'index'   => array(
        'ind_barcode'     => array(
            'columns' => array(
                0 => 'goods_barcode',
            ),
        ),
        'ind_bn'         => array(
            'columns' => array(
                0 => 'purchases_receipts_bn',
            ),
        ),
        'ind_goods_bn'     => array(
            'columns' => array(
                0 => 'goods_bn',
            ),
        ),
        'ind_name'       => array(
            'columns' => array(
                0 => 'goods_name',
            ),
        ),
        'ind_goods_id'   => array(
            'columns' => array(
                0 => 'goods_id',
            ),
        ),
        'ind_product_id' => array(
            'columns' => array(
                0 => 'product_id',
            ),
        ),
    ),
    'engine'  => 'innodb',
    'comment' => ('进货单表'),
);
