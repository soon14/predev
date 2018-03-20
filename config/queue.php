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


define('QUEUE_SCHEDULE', 'system_queue_adapter_mysql');
define('DEFAULT_PUBLISH_QUEUE', 'normal');
#define('QUEUE_CONSUMER', 'fork');
$bindings = array(
    'crontab:b2c_tasks_cleancartobject' => array(
        'slow',
    ) ,
    'crontab:base_tasks_cleankvstore' => array(
        'slow',
    ) ,
    'crontab:operatorlog_tasks_cleanlogs' => array(
        'slow',
    ) ,
    'b2c_tasks_messenger' => array(
        'quick',
    ) ,
    'importexport_tasks_runexport' => array(
        'slow',
    ) ,
    'importexport_tasks_runimport' => array(
        'slow',
    ) ,
);
$queues = array(
    'slow' => array(
        'title' => 'slow queue',
        'thread' => 3,
    ) ,
    'quick' => array(
        'title' => 'quick queue',
        'thread' => 5,
    ) ,
    'normal' => array(
        'title' => 'normal queue',
        'thread' => 3,
    ),
);
