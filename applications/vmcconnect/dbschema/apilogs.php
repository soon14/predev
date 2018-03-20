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



$db['apilogs'] = array(
    'columns' =>
    array(
        'log_id' =>
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
        'api_method' =>
        array(
            'type' => 'varchar(80)',
            'required' => true,
            'label' => ('方法'),
            'width' => 110,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'searchtype' => 'has',
        ),
        'log_date' =>
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
        'log_ip' =>
        array(
            'type' => 'bigint',
            'required' => true,
            'label' => ('IP'),
            'width' => 110,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'searchtype' => 'has',
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
        'ori_in_params' =>
        array(
            'type' => 'longtext',
            'label' => ('原始参数'),
            'in_list' => false,
            'default_in_list' => false,
        ),
        'code_in_params' =>
        array(
            'type' => 'longtext',
            'label' => ('转换后参数'),
            'in_list' => false,
            'default_in_list' => false,
        ),
        'ori_out_params' =>
        array(
            'type' => 'longtext',
            'label' => ('原始返回数据'),
            'in_list' => false,
            'default_in_list' => false,
        ),
        'code_out_params' =>
        array(
            'type' => 'longtext',
            'label' => ('转换后返回数据'),
            'in_list' => false,
            'default_in_list' => false,
        ),
    ),
    'index' =>
    array(
        'index_vmcconnect_apilogs' => array(
            'columns' => array(
                0 => 'app_key',
                1 => 'api_method',
                2 => 'log_date',
            ),
        ),
    ),
    'version' => '$Rev$',
    'comment' => ('API 服务日志'),
);
