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


$db['article_bodys'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '自增id' ,
            'pkey' => true,
            'extra' => 'auto_increment',
            'in_list' => true,
        ) ,
        'article_id' => array(
            'type' => 'table:article_indexs',
            'required' => true,
            'label' => '文章id' ,
            'in_list' => true,
        ) ,
        'setting' =>array(
            'type' => 'serialize',
            'label' => ('设置') ,
            'deny_export' => true
        ),
        'content' => array(
            'type' => 'longtext',
            'label' => '文章内容' ,
            'in_list' => true,
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
        'goods_info' => array(
            'type' => 'serialize',
            'label' => '关联产品' ,
        ) ,
        'hot_link' => array(
            'type' => 'serialize',
            'label' => '热词' ,
        ) ,
        'length' => array(
            'type' => 'int unsigned',
            'label' => '内容长度' ,
        ) ,
        'image_id' => array(
            'type' => 'varchar(32)',
            'required' => false,
            'label' => '图片id' ,
        ) ,
    ) ,
    'index' => array(
        'ind_article_id' => array(
            'columns' => array(
                0 => 'article_id',
            ) ,
            'prefix' => 'unique',
        ) ,
    ) ,
    'version' => '$Rev$',
    'comment' => '文章节点表' ,
);
