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


$db['log_site'] = array(
    'columns' => array(
        'event_id' => array(
            'type' => 'number',
            'pkey' => true,
            'extra' => 'auto_increment',
        ) ,
        'event_time' => array(
            'type' => 'varchar(50)'
        ) ,
        'member_id' => array(
            'type' => 'table:members@b2c'
        ) ,
        'event_data' => array(
            'type' => 'varchar(500)'
        ) ,
        'event_type' => array(
            'type' => 'text'
        ) ,
    ) ,
    'comment' => '前端用户登录记录' ,
);
