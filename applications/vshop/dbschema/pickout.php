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


$db['pickout'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => ('记录ID'),
        ),
        'goods_id' => array(
            'type' => 'table:goods@b2c',
            'default' => 0,
            'required' => true,
            'label' => ('商品ID'),
        ),
        'shop_id' => array(
            'type' => 'varchar(50)',
            'label' => ('店铺ID'),
            'comment' => ('店铺ID'),
            'in_list' => true,
            'default_in_list' => false,
        ),
        'order_num' => array(
            'title' => '排序' ,
            'type' => 'number',
            'default' => 0,
        ) 
    ),
    'index' => array(
        'ind_goods_id' => array(
            'columns' => array(
                0 => 'goods_id',
            ),
        ),
    ),
    'comment' => ('店铺精选表'),
);
