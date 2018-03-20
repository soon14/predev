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
$db['task_orders'] = array(
    'columns' => array(
        'order_id' => array(
            'type' => 'table:orders@b2c',
            'required' => true,
            'label' => 'order ID',
            'pkey' => true,
        ),
        'task_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'task ID',
            'pkey' => true,
        ),
        'member_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'member ID',
        ),
        'createtime' => array(
            'type' => 'time',
            'required' => true,
            'default' => 0,
            'label' => '创建时间',
        ),
    )
);
