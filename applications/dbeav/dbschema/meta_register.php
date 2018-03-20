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


 
$db['meta_register']=array (
  'columns' => 
  array (
    'mr_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'meta id',
      'width' => 110,
      'comment' => 'meta id',
      
      'in_list' => true,
      'default_in_list' => true,
    ),
    'tbl_name' => 
    array (
      'type' => 'varchar(100)',
      'required' => true,
      'label' => '表名',
      'width' => 110,
      'comment' => '表名',
      
      'in_list' => true,
      'is_title' => true,
    ),
    'pk_name' => 
    array (
      'type' => 'varchar(255)',
      'required' => true,
      'label' => '主表主键名',
      'width' => 110,
      'comment' => '主表主键名',
      
      'in_list' => true,
    ),
    'col_name' => 
    array (
      'type' => 'varchar(100)',
      'required' => true,
      'label' => '字段名',
      'width' => 110,
      'comment' => '字段名',
      
      'in_list' => true,
    ),
    'col_type' => 
    array (
      'type' => 'varchar(255)',
      'required' => true,
      'label' => '字段类型',
      'width' => 110,
      'comment' => '字段类型',
      
      'in_list' => true,
    ),
    'col_desc' => 
    array (
      'type' => 'serialize',
      'required' => true,
      'label' => '字段描述',
      'width' => 110,
      'comment' => '字段描述',
      
      'in_list' => true,
    ),
  ),
  'comment' => 'meta关联表',
  'index' => 
  array (
    'idx_tbl_name' => 
    array (
      'columns' => 
      array (
        0 => 'tbl_name',
      ),
    ),
    'idx_col_name' => 
    array (
      'columns' => 
      array (
        0 => 'col_name',
      ),
    ),
    'idx_tbl_col' => 
    array (
      'columns' => 
      array (
        0 => 'tbl_name',
        1 => 'col_name',
      ),
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 43312 $',
  'comment' => 'meta注册主表',
);
