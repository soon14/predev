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



$db['hooks'] = array(
    'columns' =>
    array(
        'hook_id' =>
        array(
            'type' => 'smallint(5) unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => ('hook_key'),
            'width' => 110,
            'filtertype' => 'yes',
            'searchtype' => 'has',
        ),
        'app_id' =>
        array(
            'type' => 'smallint(5) unsigned',
            'required' => true,
            'label' => ('app_key'),
            'width' => 110,
            'is_title' => false,
            'editable' => false,
            'in_list' => false,
            'default_in_list' => false,
        ),
        'hook_name' =>
        array(
            'type' => 'varchar(80)',
            'label' => ('HOOK服务名称'),
            'width' => 180,
            'is_title' => true,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'searchtype' => 'has',
        ),
        'hook_url' =>
        array(
            'type' => 'varchar(200)',
            'label' => ('HOOK服务URL'),
            'width' => 180,
            'is_title' => false,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'searchtype' => 'has',
        ),
        'hook_msg_tpl' =>
        array(
            'type' => 'varchar(20)',
            'label' => ('消息模版'),
            'default' => 'def',
            'width' => 180,
            'in_list' => true,
            'default_in_list' => false,
        ),
        'hook_addon' =>
        array(
            'type' => 'text',
            'required' => true,
            'default' => '',
            'in_list' => false,
            'default_in_list' => false,
            'label' => 'HOOK服务附加参数',
        ),
        'hook_status' =>
        array(
            'type' => 'tinyint(1) unsigned',
            'default' => '0',
            'label' => ('启用状态'),
            'in_list' => true,
            'default_in_list' => false,
        ),
        'hook_order' =>
        array(
            'type' => 'smallint(4) unsigned',
            'default' => 2000,
            'label' => ('排序'),
            'is_title' => false,
            'editable' => false,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'hook_alert_phone' =>
        array(
            'type' => 'varchar(11)',
            'default' => '',
            'label' => ('预警号码'),
            'width' => 180,
            'is_title' => true,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'searchtype' => 'has',
        ),
    ),
    'index' =>
    array(
        'Index_vmcconnect_app_hooks' =>
        array(
            'columns' =>
            array(
                0 => 'app_id',
                1 => 'hook_url',
            ),
            'prefix' => 'UNIQUE',
        ),
    ),
    'version' => '$Rev$',
    'comment' => ('应用HOOK表'),
);
