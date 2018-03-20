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



$db['register']=array (
    'columns' =>
    array (
        'id' =>
        array (
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'app' =>
        array (
            'type' => 'varchar(50)',
            'required' => true,
            'label' => '程序目录',
        ),
        'ctl' =>
        array (
            'type' => 'varchar(50)',
            'required' => true,
            'label' => '控制器',
        ),
        'act' =>
        array (
            'type' => 'varchar(50)',
            'required' => false,
            'label' => '动作',
        ),
        'method' =>
        array (
          'type' =>
          array (
            'post' => ('post方法'),
            'get' => ('get方法'),
          ),
          'default' => 'post',
          'required' => true,
          'label' => ('提交方法'),
        ),
        'module' =>
        array (
            'type' => 'varchar(255)',
            'required' => true,
            'label' => '日志模块',
        ),
        'operate_type' =>
        array (
            'type' => 'varchar(255)',
            'required' => true,
            'label' => '操作类型',
        ),
        'template' =>
        array (
            'type' => 'varchar(255)',
            'required' => false,
            'label' => '模板',
        ),
        'param' =>
        array (
            'type' => 'varchar(255)',
            'required' => false,
            'label' => '参数',
        ),
        'prk' =>
        array (
            'type' => 'varchar(255)',
            'required' => false,
            'default' => '0',
            'label' => '修改项唯一值',
        ),
    ),
    'index' =>
    array (
        'ind_index' =>
        array (
          'columns' =>
          array (
            0 => 'app',
            1 => 'ctl',
            2 => 'act',
          ),
          'prefix' => 'UNIQUE',
        ),
    ),
);
