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


$db['xcxerrorlog'] = array(
    'columns' => array(
        'id' => array(
          'type' => 'int unsigned',
          'required' => true,
          'pkey' => true,
          'extra' => 'auto_increment',
          'comment' => 'ID',
        ),
        'appid' => array(
            'type' => 'varchar(100)',
            'label' => '小程序APPID',
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'network_type' => array(
            'type' => 'varchar(20)',
            'label' => '网络类型',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'system_model' => array(
            'type' => 'varchar(50)',
            'label' => '手机型号',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'system_systemver' => array(
            'type' => 'varchar(50)',
            'label' => '操作系统版本',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'system_platform' => array(
            'type' => 'varchar(50)',
            'label' => '客户端平台',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'system_sdkver' => array(
            'type' => 'varchar(50)',
            'label' => '客户端基础库版本',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'system_ver' => array(
            'type' => 'varchar(50)',
            'label' => '微信版本号',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'system_screen' => array(
            'type' => 'varchar(50)',
            'label' => '屏幕宽高',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'system_window' => array(
            'type' => 'varchar(50)',
            'label' => '可使用窗口宽高',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'system_pixel' => array(
            'type' => 'varchar(50)',
            'label' => '设备像素比',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'system_language' => array(
            'type' => 'varchar(50)',
            'label' => '微信设置的语言',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'error_log' => array(
            'type' => 'text',
            'label' => '错误日志内容',
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'timestamp' => array(
            'type' => 'time',
            'label' => ('上报时间'),
            'order' => true,
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
    ),
    'comment' => '小程序错误日志',
);
