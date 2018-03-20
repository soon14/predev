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


$db['article_indexs'] = array(
    'columns' => array(
        'article_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'ID' ,
            'pkey' => true,
            'extra' => 'auto_increment',

            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'title' => array(
            'type' => 'varchar(200)',
            'required' => true,
            'label' => '标题' ,
            'searchtype' => 'has',
            'filtertype' => 'yes',

            'in_list' => true,
            'default_in_list' => true,
            'is_title' => true,
        ) ,
        'platform' => array(
            'type' => array(
                'all' => '所有终端' ,
                // 'pc' => 'PC端',
                // 'mobile' => '移动端',

            ) ,
            'default'=>'all',
            'label' => '可见终端' ,
            'required' => true,
        ) ,
        'type' => array(
            'type' => array(
                '1' => '普通文章' ,
                '2' => '完全自定义页' ,
            ) ,
            'label' => '页面类型' ,
            'required' => true,
            'default' => 1,
            'filtertype' => 'yes',

            'in_list' => true,
            'default_in_list' => false,
        ) ,
        'node_id' => array(
            'type' => 'table:article_nodes',
            'required' => true,
            'label' => '栏目' ,
            'filtertype' => 'yes',

            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'author' => array(
            'type' => 'varchar(50)',
            'label' => '作者' ,
            'searchtype' => 'has',
            'filtertype' => 'yes',

            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'pubtime' => array(
            'type' => 'time',
            'label' => '发布时间' ,
            'filtertype' => 'yes',

            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'uptime' => array(
            'type' => 'time',
            'label' => '更新时间' ,

            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'level' => array(
            'type' => array(
                '1' => '普通' ,
                '2' => '重要' ,
            ) ,
            'label' => '文章等级' ,
            'required' => true,
            'filtertype' => 'yes',
            'filterdefault' => false,
            'default' => 1,
        ) ,
        'ifpub' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 'false',
            'label' => '发布' ,
            'in_list' => true,
            'filtertype' => 'yes',
            'filterdefault' => false,
            'default_in_list' => true,
        ) ,
        'ordernum' => array(
            'type' => 'number',
            'required' => true,
            'orderby'=>true,
            'in_list' => true,
            'default_in_list' => true,
            'default' => 0,
            'label' => '排序' ,
        ) ,
        'pv' => array(
            'type' => 'int unsigned',
            'default' => 0,
            'label' => 'pageview',

        ) ,
        'disabled' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 'false',
        ) ,
    ) ,
    'comment' => '文章主表' ,
    'index' => array(
        'ind_node_id' => array(
            'columns' => array(
                0 => 'node_id',
            ) ,
        ) ,
        'ind_ifpub' => array(
            'columns' => array(
                0 => 'ifpub',
            ) ,
        ) ,
        'ind_pubtime' => array(
            'columns' => array(
                0 => 'pubtime',
            ) ,
        ) ,
        'ind_level' => array(
            'columns' => array(
                0 => 'level',
            ) ,
        ) ,
        'ind_disabled' => array(
            'columns' => array(
                0 => 'disabled',
            ) ,
        ) ,
        'ind_pv' => array(
            'columns' => array(
                0 => 'pv',
            ) ,
        ) ,
    ) ,
    'version' => '$Rev$',
    'comment' => '文章主表' ,
);
