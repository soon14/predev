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


$db['log_desktop'] = array(
    'columns' => array(
        'event_id' => array(
            'type' => 'number',
            'pkey' => true,
            'extra' => 'auto_increment',
        ) ,
        'event_time' => array(
            'type' => 'varchar(50)'
        ) ,
        'event_data' => array(
            'type' => 'varchar(500)'
        ) ,
        'event_type' => array(
            'type' => 'text'
        ) ,
    ) ,
    'comment' => '后端用户登录记录' ,
);
