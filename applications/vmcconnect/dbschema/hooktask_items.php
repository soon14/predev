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



$db['hooktask_items'] = array(
    'columns' =>
    array(
        'item_id' =>
        array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => ('logID'),
        ),
        'app_key' =>
        array(
            'type' => 'smallint(5) unsigned',
            'required' => true,
            'label' => ('app_key'),
            'width' => 110,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'searchtype' => 'has',
        ),
        'hook_key' =>
        array(
            'type' => 'smallint(5) unsigned',
            'required' => true,
            'label' => ('hook_key'),
            'width' => 110,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'searchtype' => 'has',
        ),
        'task_id' =>
        array(
            'type' => 'smallint(5) unsigned',
            'required' => true,
            'label' => ('task_id'),
            'width' => 110,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'no',
            'searchtype' => 'has',
        ),
        'task_type' =>
        array(
            'type' => 'varchar(80)',
            'required' => true,
            'label' => ('HOOK类型'),
            'width' => 110,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'searchtype' => 'has',
        ),
        'app_secret' =>
        array(
            'type' => 'varchar(40)',
            'required' => true,
            'label' => ('加密串'),
            'width' => 110,
            'in_list' => false,
            'default_in_list' => false,
        ),
        'send_params' =>
        array(
            'type' => 'longtext',
            'required' => false,
            'label' => ('发送内容'),
            'width' => 110,
        ),
        'send_date' =>
        array(
            'type' => 'int unsigned',
            'required' => true,
            'label' => ('时间'),
            'width' => 110,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'no',
            'searchtype' => 'has',
        ),
        'exception_msg' =>
            array(
            'type' => 'longtext',
            'comment' => '异常信息',
            'label' => '异常信息',
            'in_list' => true,
            'default_in_list' => true,
            ),
        'act_res' =>
            array(
            'type' => 'tinyint(1) unsigned',
            'required' => true,
            'label' => ('操作结果'),
            'width' => 110,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'no',
            'searchtype' => 'has',
            ),
    ),
    'index' =>
    array(
        'Index_vmcconnect_hooktask_task_id' => array(
            'columns' => array(
                0 => 'task_id',
            ),
        ),
        'Index_vmcconnect_hooktask_app_key' => array(
            'columns' => array(
                0 => 'app_key',
            ),
        ),
        'Index_vmcconnect_hooktask_hook_key' => array(
            'columns' => array(
                0 => 'hook_key',
            ),
        ),
    ),
    'version' => '$Rev$',
    'comment' => ('HOOK 任务 item'),
);
