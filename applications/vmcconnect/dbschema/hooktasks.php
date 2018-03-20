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



$db['hooktasks'] = array(
    'columns' =>
    array(
        'task_id' =>
        array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => ('logID'),
        ),
        'task_type' =>
        array(
            'type' => 'varchar(100)',
            'required' => true,
            'label' => ('任务类型'),
            'width' => 110,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'searchtype' => 'has',
        ),
        'task_data' =>
        array(
            'type' => 'longtext',
            'required' => true,
            'label' => ('任务数据'),
            'width' => 110,
        ),
        'task_status' =>
        array(
            'type' => 'tinyint(1) unsigned',
            'default' => '0',
            'label' => ('状态'),
            'in_list' => true,
            'default_in_list' => true,
        ),
    ),
    'index' =>
    array(
    ),
    'version' => '$Rev$',
    'comment' => ('HOOK 任务'),
);
