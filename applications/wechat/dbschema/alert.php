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


$db['alert'] = array(
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
            'required' => true,
            'default' => '',
            'label' => '公众号ID',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'errortype' => array(
            'type' => 'int(10)',
            'required' => true,
            'default' => 0,
            'label' => '错误编码',
            'in_list' => true,
            'default_in_list' => true,

        ),
        'description' => array(
            'type' => 'longtext',
            'required' => true,
            'default' => '',
            'order' => 10,
            'label' => '错误描述',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'alarmContent' => array(
            'type' => 'longtext',
            'label' => ('错误详情'),
        ),
        'timestamp' => array(
            'type' => 'time',
            'label' => ('创建时间'),
            'in_list' => true,
            'default_in_list' => true,
        ),
    ),
    'version' => '$Rev: 40918 $',
    'comment' => '告警消息',
);
