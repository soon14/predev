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



$db['apps'] = array(
    'columns' =>
    array(
        'app_id' =>
        array(
            'type' => 'smallint(5) unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => ('app_key'),
            'width' => 110,
            'filtertype' => 'yes',
            'searchtype' => 'has',
        ),
        'app_name' =>
        array(
            'type' => 'varchar(80)',
            'label' => ('应用名称'),
            'width' => 180,
            'is_title' => true,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'searchtype' => 'has',
        ),
        'app_secret' =>
        array(
            'type' => 'varchar(80)',
            'label' => ('加密串'),
            'width' => 180,
            'in_list' => false,
        ),
        'app_com_tpl' =>
        array(
            'type' => 'varchar(20)',
            'label' => ('命令模版'),
            'default' => 'def',
            'width' => 180,
            'in_list' => true,
            'default_in_list' => false,
        ),
        'app_desc' =>
        array(
            'type' => 'varchar(200)',
            'label' => ('应用介绍'),
            'width' => 180,
            'in_list' => false,
        ),
        'app_status' =>
        array(
            'type' => 'tinyint(1) unsigned',
            'default' => '0',
            'label' => ('启用状态'),
            'in_list' => true,
            'default_in_list' => false,
        ),
        'app_order' =>
        array(
            'type' => 'smallint(4) unsigned',
            'default' => 2000,
            'label' => ('排序'),
            'is_title' => false,
            'editable' => false,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'app_api_status' =>
        array(
            'type' => 'tinyint(1) unsigned',
            'default' => '0',
            'label' => ('API启用状态'),
            'in_list' => true,
            'default_in_list' => false,
        ),
        'app_hook_status' =>
        array(
            'type' => 'tinyint(1) unsigned',
            'default' => '0',
            'label' => ('HOOK启用状态'),
            'in_list' => true,
            'default_in_list' => false,
        ),
        'api_alert_phone' =>
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
        'Index_vmcconnect_app_status' =>
        array(
            'columns' =>
            array(
                0 => 'app_status',
                0 => 'app_order',
            ),
        ),
    ),
    'version' => '$Rev$',
    'comment' => ('应用配置表'),
);
