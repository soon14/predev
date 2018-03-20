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


$db['user_lv'] = array(
  'columns' => array(
    'user_lv_id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
    ),
    'name' => array(
      'type' => 'varchar(100)',
      'is_title' => true,
      'required' => true,
      'default' => '',
      'label' => ('等级名称'),
      'in_list' => true,
      'default_in_list' => true,
    ),
    'lv_logo' => array(
      'type' => 'varchar(255)',
      'label' => ('等级LOGO'),
      'in_list' => false,
      'default_in_list' => false,
    ),
    'allow_publish' => array(
      'type' => 'bool',
      'default' => 'true',
      'required' => true,
      'label' => '是否允许发起主题',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'allow_follow' => array(
      'type' => 'bool',
      'default' => 'true',
      'required' => true,
      'label' => '是否允许评论',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'allow_audio' => array(
      'type' => 'bool',
      'default' => 'true',
      'required' => true,
      'label' => '是否可发布语音',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'allow_image' => array(
      'type' => 'bool',
      'default' => 'true',
      'required' => true,
      'label' => '是否可发布图片',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'allow_shortvideo' => array(
      'type' => 'bool',
      'default' => 'true',
      'required' => true,
      'label' => '是否可发布短视频',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'allow_svideo' => array(
      'type' => 'bool',
      'default' => 'true',
      'required' => true,
      'label' => '是否可在PC端发布视频',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'default_lv' => array(
      'type' => 'intbool',
      'default' => '0',
      'required' => true,
      'label' => '是否默认',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'lv_remark' => array(
      'type' => 'text',
      'label' => '会员等级备注',
    ),
  ),
  'index' => array(
    'ind_name' => array(
      'columns' => array(
        0 => 'name',
      ),
      'prefix' => 'UNIQUE',
    ),
  ),
  'engine' => 'innodb',
  'comment' => '会员等级表',
);
