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

$db['group_members'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'ID',
            'pkey' => true,
            'extra' => 'auto_increment',
            'in_list' => true,
        ),
        'member_id' => array(
            'type' => 'table:members@b2c',
            'required' => true,
            'in_list' => true,
        ),
        'group_id' => array(
            'type' => 'table:group',
            'required' => true,
            'in_list' => true,
        ),

    ),
    'comment' => ('用户'),
);
