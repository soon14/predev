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


$db['income_receipts_item'] = array(
    'columns' => array(
        'item_id'        => array(
            'type'    => 'number',
            'pkey'    => true,
            'extra'   => 'auto_increment',
            'comment' => ('损益单详单主键id'),
        ),
        'income_receipts_bn'             => array(
            'type'     => 'char(32)',
            'required' => true,
            'default'  => '',
            'label'    => '损益单编号',
            'comment'  => '损益单编号',
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
        'goods_old_store'                => array(
            'type'            => 'int',
            'required'        => true,
            'default'         => '0',
            'label'           => '商品库存',
            'comment'         => '商品在数据库里的库存',
        ),
        'goods_real_store'                => array(
            'type'            => 'int',
            'required'        => true,
            'default'         => '0',
            'label'           => '实际库存',
            'comment'         => '商品现在实际的库存',
        ),
        'goods_check_profit_num'                => array(
            'type'            => 'int',
            'required'        => true,
            'default'         => '0',
            'label'           => '盘盈件数',
            'comment'         => '商品销售的数量',
        ),
        'goods_check_loss_num'                => array(
            'type'            => 'int',
            'required'        => true,
            'default'         => '0',
            'label'           => '盘损件数',
            'comment'         => '商品因各种原因损失的数量',
        ),
        'income_goods_money'       => array(
            'type'            => 'money',
            'required'        => true,
            'default'         => '0',
            'label'           => '商品单价',
            'comment'         => '损益商品单价',
            'in_list'         => true,
            'default_in_list' => true,
        ),
        'goods_check_profit_money'       => array(
            'type'            => 'money',
            'required'        => true,
            'default'         => '0',
            'label'           => '盘盈余额',
            'comment'         => '盘盈商品总金额',
        ),
        'goods_check_loss_money'       => array(
            'type'            => 'money',
            'required'        => true,
            'default'         => '0',
            'label'           => '盘损金额',
            'comment'         => '商品因各种原因损失的总金额',
        ),
        'income_remark'   => array(
            'type'            => 'text',
            'label'           => '备注',
            'comment'         => '损益备注',
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
                0 => 'income_receipts_bn',
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
    'comment' => ('损益单细单表'),
);
