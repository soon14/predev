<?php
$db['seo']=array (
    'columns' =>
    array (
        'id' =>
        array (
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => 'ID',
        ),
        'app' =>
        array (
            'type' => 'varchar(50)',
            'required' => true,
            'label' => '程序目录',
            'comment' => '应用(app)',
        ),
        'ctl' =>
        array (
            'type' => 'varchar(50)',

            'label' => '控制器',
        ),
        'act' =>
        array (
            'type' => 'varchar(50)',
            'required' => true,
            'label' => '路径标识',
        ),
        'title' =>
        array (
            'type' => 'varchar(50)',
            'required' => true,
            'label' => '应用页面',
            'default_in_list'=>true,
            'in_list'=>true,
        ),
        'config' =>
        array (
            'type' => 'serialize',
            'default' => '',
            'label' => '配置',
        ),
        'param' =>
        array (
            'type' => 'serialize',
            'default' => '',
            'label' => '参数',
        ),
        'update_modified' =>
        array (
          'type' => 'time',

		  'comment' => '更新时间',
        ),
    ),
    'comment' => '前台SEO配置表',
);
