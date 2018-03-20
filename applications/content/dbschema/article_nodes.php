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


$db['article_nodes'] = array(
    'columns' => array(
        'node_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '栏目id' ,
            'pkey' => true,
            'extra' => 'auto_increment',
            'in_list' => true,
        ) ,
        'parent_id' => array(
            'type' => 'number',
            'required' => true,
            'default' => 0,
            'label' => '父栏目' ,
            'in_list' => true,
        ) ,
        'node_depth' => array(
            'type' => 'tinyint(1)',
            'required' => true,
            'default' => 0,
            'label' => '栏目深度' ,
        ) ,
        'node_name' => array(
            'type' => 'varchar(50)',
            'required' => true,
            'default' => '',
            'label' => '栏目名称' ,
            'is_title' => true,
            'default_in_list' => true,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'node_pagename' => array(
            'type' => 'varchar(50)',
            'label' => '栏目页面名' ,
            'in_list' => true,
        ) ,
        'node_path' => array(
            'type' => 'varchar(200)',
            'label' => '栏目路径' ,
            'in_list' => false,
        ) ,
        'seo_title' => array(
            'type' => 'varchar(100)',
            'label' => 'SEO标题' ,
        ) ,
        'seo_description' => array(
            'type' => 'mediumtext',
            'label' => 'SEO简介' ,
        ) ,
        'seo_keywords' => array(
            'type' => 'varchar(200)',
            'label' => 'SEO关键字' ,
        ) ,
        'has_children' => array(
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'label' => '是否存在子栏目' ,
            'in_list' => false,
        ) ,
        'ifpub' => array(
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'label' => '发布' ,
            'in_list' => true,
        ) ,
        'hasimage' => array(
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'label' => '图' ,
            'in_list' => true,
        ) ,
        'ordernum' => array(
            'type' => 'number',
            'required' => true,
            'default' => 0,
            'label' => '排序' ,
        ) ,
        'homepage' => array(
            'type' => 'bool',
            'default' => 'false',
            'label' => '主页' ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'uptime' => array(
            'type' => 'time',
            'label' => '修改时间' ,
        ) ,
        'setting' =>array(
            'type' => 'serialize',
            'label' => ('设置') ,
            'deny_export' => true
        ),
        'list_tmpl_path' => array(
            'type' => 'varchar(50)',
            'label' => '列表页模板' ,
        ) ,
        'content' => array(
            'type' => 'longtext',
            'label' => '栏目页内容' ,
        ) ,
        'disabled' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 'false',
        ) ,
    ) ,
    'index' => array(
        'ind_disabled' => array(
            'columns' => array(
                0 => 'disabled',
            ) ,
        ) ,
        'ind_ordernum' => array(
            'columns' => array(
                0 => 'ordernum',
            ) ,
        ) ,
    ) ,
    'comment' => '栏目表' ,
);
