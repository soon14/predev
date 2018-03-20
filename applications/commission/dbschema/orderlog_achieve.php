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


$db['orderlog_achieve'] = array(
    'columns' => array(
        'achieve_id' => array(
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
        'member_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '分佣者ID',
            'comment' => '分佣者ID',
        ),
        'achieve_fund' => array(
            'type' => 'money',
            'required' => true,
            'label' => '获取的佣金',
            'comment' => '获取的佣金',
        ),
        'parent_type' => array(
            'type' => 'varchar(10)',
            'default' => 'first',
            'required' => true,
            'label' => '身份',
            'comment' => '获取分佣者在本单的身份',
        ),
    ),
    'index' => array(
        'ind_orderlog_id' => array(
            'columns' =>
                array(
                    0 => 'orderlog_id',
                ),
        )
    ),
    'comment' => '分佣流向记录表',
);
