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


$db['themes'] = array(
  'columns' => array(
      'theme_id' => array(
        'type' => 'varchar(100)',
        'required' => true,
        'pkey' => true,
        'label' => '模板ID',
        'width' => '90',
        'in_list' => true,
        'default_in_list' => true,
        'comment' => '模板ID',
      ),
    'theme_dir' => array(
      'type' => 'varchar(80)',
      'required' => true,
      'pkey' => true,
      'label' => '模板目录名',
      'width' => '90',
      'in_list' => true,
      'default_in_list' => true,
      'comment' => '主题唯一目录英文名称',
    ),
    'name' => array(
      'type' => 'varchar(50)',

      'is_title' => true,
      'label' => '模板名称',
      'width' => '200',
      'in_list' => true,
      'default_in_list' => true,
      'comment' => '主题名称',
    ),
    'author' => array(
      'type' => 'varchar(50)',

      'label' => '作者',
      'width' => '100',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'version' => array(
      'type' => 'varchar(50)',

      'label' => '版本',
      'width' => '80',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'info' => array(
      'type' => 'varchar(255)',

      'comment' => '详细说明',
    ),
    'config' => array(
      'type' => 'serialize',

      'comment' => '配置信息',
    ),
    'views' => array(
      'type' => 'serialize',

      'comment' => '模板文件',
    ),
    'is_used' => array(
      'type' => 'bool',

      'default' => 'false',
      'in_list' => true,
      'label' => '是否启用',
      'comment' => '是否启用',
    ),
    'last_modify' => array(
      'type' => 'last_modify',
      'label' => '最后更新时间',
      'in_list' => true,
      'default_in_list' => true,
    ),

  ),
  'unbackup' => true,
  'comment' => '触屏端模板表',
);
