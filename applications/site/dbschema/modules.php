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



$db['modules']=array (
    'columns' =>
    array (
        'id' =>
        array (
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => '模块ID',
        ),
        'app' =>
        array (
            'type' => 'varchar(50)',
            'default' => '',
            'required' => true,
            'label' => '程序目录',
            'width'=>80,
            'default_in_list'=>true,
            'in_list'=>true,
            'comment' => '应用(app)',
        ),
        'ctl' =>
        array (
            'type' => 'varchar(50)',
            'default' => '',
            'required' => true,
            'label' => '控制器',
            'width'=>80,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'path' =>
        array (
            'type' => 'varchar(50)',
            'default' => '',
            'required' => true,
            'label' => '路径标识',
            'width'=>80,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'extension' =>
        array (
            'type' => 'varchar(10)',
            'default' => '',
            'label' => '扩展名',
            'width'=>50,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'title' =>
        array (
            'type' => 'varchar(50)',
            'default' => '',
            'required' => true,
            'label' => '名称',
            'width' => 100,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'allow_menus'=>
        array (
            'type' => 'varchar(255)',
            'default' => '',
            'required' => true,
            'label' => '允许菜单',
            'width' => 200,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'is_native'=>
        array (
            'type' => 'bool',
            'required' => true,
            'default'=>'false',
            'label'=>'原生模块',
            'width'=>80,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'enable' =>
        array (
            'type' => 'bool',
            'required' => true,
            'default'=>'false',
            'label'=>'启用',
            'width'=>80,
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'use_ssl' =>
        array (
            'type' => 'bool',
            'required' => true,
            'default'=>'false',
            'label'=>'HTTPS',
            'width'=>80,
            'default_in_list'=>true,
            'in_list'=>true,
            'comment' => '是否使用SSL',
        ),
        'update_modified' =>
        array (
          'type' => 'time',
          
		  'comment' => '更新时间',
        ),
    ),
    'comment' => '前台模块表',
);
