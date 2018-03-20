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



$db['hook_items'] = array(
    'columns' =>
    array(
        'app_id' =>
        array(
            'type' => 'smallint(5) unsigned',
            'required' => true,
            'pkey' => true,
            'label' => ('app_key'),
            'width' => 110,
            'is_title' => false,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'hook_id' =>
        array(
            'type' => 'smallint(5) unsigned',
            'required' => true,
            'pkey' => true,
            'label' => ('hook_id'),
            'width' => 110,
            'is_title' => false,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'hook_item' =>
        array(
            'type' => 'varchar(80)',
            'required' => true,
            'pkey' => true,
            'label' => ('可用命令'),
            'width' => 180,
            'is_title' => false,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
    ),
    'version' => '$Rev$',
    'comment' => ('已选HOOK表'),
);
