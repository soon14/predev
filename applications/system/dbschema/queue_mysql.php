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


$db['queue_mysql'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => 'ID',
        ),
        'queue_name' => array(
            'type' => 'varchar(100)',
            'comment' => '队列标识',
            'label' => '队列标识',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'worker' => array(
            'type' => 'varchar(100)',
            'required' => true,
            'comment' => '执行任务类',
            'label' => '执行任务类',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'params' => array(
            'type' => 'longtext',
            'required' => true,
            'default'=>'a:0:{}',
            'comment' => '任务参数',
            'label' => '任务参数',
        ),
        'create_time' => array(
            'type' => 'time',
            'default' => 0,
            'comment' => '进入队列的时间',
            'label' => '进入队列的时间',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'last_cosume_time' => array(
            'type' => 'time',
            'default' => 0,
            'comment' => '任务开始执行时间',
            'label' => '任务开始执行时间',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'owner_thread_id' => array(
            'type' => 'int',
            'default' => -1,
            'comment' => '进程',
            'label' => '进程',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'exception_msg' => array(
            'type' => 'longtext',
            'comment' => '异常信息',
            'label' => '异常信息',
            'in_list' => true,
            'default_in_list' => true,
        ),
    ),
    'index' => array(
        'ind_get' => array(
            'columns' => array(
                0 => 'queue_name',
                1 => 'owner_thread_id',
            ),
        ),
    ),
    'engine' => 'innodb',
    'ignore_cache' => true,
    'comment' => '队列-mysql实现表',
);
