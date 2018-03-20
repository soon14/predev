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


$db['order_items'] = array(
    'columns' => array(
        'item_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => 'ticket_id',
        ),
        'order_id' => array(
            'type' => 'table:activity_order',
            'label' => '订单号',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        ),
        'sn' => array(
            'type' => 'varchar(20)',
            'label' => '编号',
            'required' => true,
            'in_list' => true,
        ),
        'status' => array(
            'type' => array(
                '0' =>'未使用',
                '1' =>'已使用'
            ),
            'label' => '票券状态',
            'required' => true,
            'default' =>'0'
        ),
    ),
    'comment' => ('票券编号'),
);
