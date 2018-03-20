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
$db['products_extend'] = array(
    'columns' => array(
        'product_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => '关联products',
            'pkey' => true,
        ),
        'goods_id' => array(
            'type' => 'number',
            'required' => true,
        ),
        'sku_bn' => array(
            'type' => 'varchar(50)',
            'label' => ('SKU货号'),
            'required' => true,
            'comment' => ('SKU货号'),
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        ),
        'title' => array(
            'type' => 'varchar(255)',
            'label' => ('商品名称'),
            'comment' => '商品名称规格',
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
            'order' => '1',
        ),
        'o2ocds_value' => array(
            'type' => 'serialize',
            'comment' => ('分佣比例或金额array(first => value1 ,second=>value2)')
        ),
        'lv_o2ocds_value' => array(
            'type' => 'serialize',
            'comment' => ('分佣比例或金额array(lv0 => value0 ,lv1=>value1 ...)')
        ),
    )
);