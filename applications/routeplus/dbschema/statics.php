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

$db['statics'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'url' => array(
            'type' => 'varchar(255)',
            'required' => true,
            'label' => '原URL',
            'default_in_list' => true,
            'in_list' => true,
            'searchtype' => 'has',
        ),
        'custom_url' => array(
            'type' => 'varchar(255)',
            'required' => true,
            'label' => '自定义URL',
            'default_in_list' => true,
            'in_list' => true,
            'searchtype' => 'has',
        ),
        'mark' => array(
            'type' => 'varchar(255)',
            'label' => '备注',
            'default_in_list' => true,
            'in_list' => true,
            'searchtype' => 'has',
        ),
        'enable' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 'true',
            'label' => '是否生效',
            'default_in_list' => true,
            'in_list' => true,
        ),
    ),
    'comment' => '静态路由表',
);
