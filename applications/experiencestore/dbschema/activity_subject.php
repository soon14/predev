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


$db['activity_subject'] = array(
  'columns' => array(
    'id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'activity_id',
    ),
    'code' => array(
      'type' => 'varchar(100)',
      'label' => '主题编码',
      'searchtype' => 'has',
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'title' => array(
      'type' => 'varchar(255)',
      'label' => '主题',
      'is_title' => true,
      'required' => true,
      'searchtype' => 'has',
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'keywords' => array(
      'type' => 'text',
      'label' => '关键词',
      'searchtype' => 'has',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'intro' => array(
      'type' => 'text',
      'label' => '简介',
      'searchtype' => 'has',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'default_image_id' => array(
        'type' => 'varchar(32)',
        'label' => '图标',
    ),
    'setting' => array(
      'type' => 'serialize',
      'label' => '设置',
    ),
    'desc' => array(
      'type' => 'longtext',
      'label' => '详细介绍',
    ),
    'sort' => array(
      'type' => 'number',
      'label' => '排序',
      'default'=>0
    ),
    'createtime' => array(
      'type' => 'time',
      'label' => '创建时间',
    ),
    'lastmodify' => array(
      'type' => 'last_modify',
      'label' => '最后更新时间',
    ),
  ),
  // //索引
  // 'index' => array(
  //   'ind_key' => array(
  //     'columns' => array(
  //       0 => 'column',
  //     ),
  //   ),
  // ),
  'comment' => ('活动主题表'),
);
