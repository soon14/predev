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


$db['orderlog_items'] = array(
    'columns' => array(
        'items_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'ID',
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'orderlog_id' => array(
            'type' => 'table:orderlog',
            'required' => true,
            'label' => '订单ID',
            'comment' => '订单ID',
        ),
        'product_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '产品ID',
            'comment' => '产品ID',
        ),
        'goods_id' => array(
            'type' => 'table:goods@b2c',
            'required' => true,
            'default' => 0,
            'comment' => ('商品ID') ,
        ) ,
        'bn' => array(
            'type' => 'varchar(40)',
            'comment' => ('SKU货号') ,
        ) ,
        'name' => array(
            'type' => 'varchar(200)',
            'comment' => ('明细商品的名称') ,
        ) ,
        'spec_info' => array(
            'type' => 'varchar(200)',
            'comment' => ('商品规格描述') ,
        ) ,
        'image_id' => array(
            'type' => 'table:image@image',
            'required' => true,
            'default' => 0,
            'comment' => '图片ID',
        ) ,
        'price' => array(
            'type' => 'money',
            'required' => true,
            'label' => '零售价',
            'comment' => '零售价',
        ),
        'buy_price' => array(
            'type' => 'money',
            'required' => true,
            'label' => '成交价',
            'comment' => '成交价',
        ),
        'product_fund' => array(
            'type' => 'money',
            'required' => true,
            'label' => '单品分佣金额',
            'comment' => '单品分佣金额',
        ),
        'o2ocds' => array(
            'type' => 'serialize',
            'required' => true,
            'label' => '佣金方式',
            'comment' => '佣金方式',
        ),
        'o2ocds_items' => array(
            'type' => 'serialize',
            'required' => true,
            'label' => '详细信息',
            'comment' => '佣金详情',
        ),
        'nums' => array(
            'type' => 'number',
            'required' => true,
            'label' => '产品数量',
            'comment' => '产品数量',
        )
    ),
    'comment' => '订单分佣明细表',

);
