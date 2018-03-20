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

$db['failure_count'] = array(
    'columns' =>
        array(
        'item_id' =>
            array(
                'type' => 'smallint(5) unsigned',
                'required' => true,
                'pkey' => true,
                'extra' => 'auto_increment',
                'label' => ('failure_id'),
            ),
        'app_key' =>     //哪一个api
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
        'hook_key' =>    //哪一个hook
            array(
                'type' => 'smallint(5) unsigned',
                'label' => ('hook_key'),
                'width' => 110,
                'in_list' => true,
                'default_in_list' => true,
                'filtertype' => 'yes',
                'searchtype' => 'has',
            ),
        'service_type' =>
            array(
                'type' =>  'enum(\'api\',\'hook\')',
                'required' => true,
                'in_list' => true,
                'label' => ('服务类型'),
            ),
        'worker' =>
            array(
                'type' => 'varchar(80)',
                'required' => true,
                'in_list' => true,
                'label' => ('方法名称'),
            ),
        'failure_count' =>      //执行失败次数
            array(
                'type' => 'bigint unsigned',
                'required' => true,
                'default' => 0,
                'label' => ('失败次数'),
            ),
        'warning_count' =>      //统计警报次数
            array(
                'type' => 'bigint unsigned',
                'required' => true,
                'default' => 0,
                'label' => ('警报次数'),
            ),
        'alert_phone' =>
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
        'remove' =>
            array(
                'type' =>  'bool',
                'required' => true,
                'default' => 'false',
                'label' => ('警报解除'),
            ),
        ),
    'version' => '$Rev$',
    'comment' => ('异常统计表'),
);
