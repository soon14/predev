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

$db['service_code'] = array(
    'columns' => array(
        'service_code' => array(
            'type' => 'char(6)',
            'required' => true,
            'pkey' => true,
            'label' => '服务码号',
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
        ),
        'order_id' => array(
            'type' => 'table:orders@b2c',
            'required' => true,
            'label' => '订单号',
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
        ),
        'store_id' => array(
            'type' => 'table:store',
            'label' => '关联店铺',
            'default' => 0,
            'in_list' => true,
        ),
        'enterprise_id' => array(
            'type' => 'table:enterprise',
            'label' => '关联企业',
            'default' => 0,
            'in_list' => true,
        ),
        'member_id' => array(
            'type' => 'number',
            'label' => '核销店员',
            'comment' => '核销店员id',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'recommender_member_id' => array(
            'type' => 'number',
            'label' => '分享店员',
            'comment' => '分享店员id',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'integral' => array(
            'type' => 'int(11)',
            'label' => '所获得的积分',
            'comment' => '所获得的的积分',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'status' => array(
            'type' => array(
                '0' => '未核销',
                '1' => '已核销',
                '2' => '已过期'
            ),
            'label' => '状态',
            'comment' => '服务码状态',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'createtime' => array(
            'type' => 'time',
            'label' => '服务码生成时间',
            'comment' => '服务码生成时间',
            'in_list' => true,
            'filtertype' => 'yes',
        ),
        'cancel_time' => array(
            'type' => 'time',
            'label' => '核销时间',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        )
    ),
    'index' => array(
        'order_id' => array(
            'columns' => array(
                0 => 'order_id',
            ),
        ),
    ),
);