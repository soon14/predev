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


$db['member_log'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => ('用户限购记录ID'),
        ),
        'res_id' => array(
            'type' => 'table:restrict',
            'required' => true,
            'label' => ('限购ID'),
        ),
        'member_id' => array(
            'type' => 'table:members@b2c',
            'required' => true,
            'in_list' => true,
            'label' => ('会员'),
        ),
        'product_id' => array(
            'type' => 'table:products@b2c',
            'required' => true,
            'label' => ('货品ID'),
            'in_list' => true,
        ),
        'goods_id' => array(
            'type' => 'table:goods@b2c',
            'required' => true,
            'label' => ('商品ID'),
            'in_list' => true,
        ),
        'quantity' => array(
            'type' => 'int(10)',
            'default' => 0,
            'label' => ('数量'),
            'comment' => ('数量'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'order_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'default' => 0,
            'label' => ('订单号') ,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => 'custom',

            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => ('创建时间'),
            'orderby' => true,
            'filtertype' => 'time',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'disable' => array(
            'type' => 'bool',
            'default' => 'false',
        ),
    ),

    'comment' => ('限购商品会员购买记录表'),
);
