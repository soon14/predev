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


$db['image_attach'] = array(
  'columns' => array(
    'attach_id' => array(
      'type' => 'number',
      'required' => true,

      'pkey' => true,
      'extra' => 'auto_increment',
      'comment' => '图片关联表ID',
    ),
    'target_id' => array(
      'type' => 'bigint(20)',
      'required' => true,
      'default' => 0,
      'comment' => '对象id-外键',
    ),
    'target_type' => array(
      'type' => 'varchar(20)',
      'required' => true,
      'default' => 0,
      'comment' => '对象类型',
    ),
    'image_id' => array(
      'type' => 'table:image',
      'required' => true,
      'default' => 0,
      'comment' => '图片的主键-外键关联image表',
    ),
    'image_order' => array(
      'label' => '排序',
      'type' => 'number',
      'default' => 0,
    ),
    'last_modified' => array(
      'label' => '更新时间',
      'type' => 'last_modify',
      'required' => true,
      'default' => 0,
    ),
  ),
  'index' => array(
    'index_1' => array(
      'columns' => array(
        0 => 'target_id',
        1 => 'target_type',
      ),
    ),
  ),
  'version' => '$Rev$',
  'comment' => '图片关联表',
);
