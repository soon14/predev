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


$db['country'] = array(
  'columns' => array(
    'ct_abbreviation' => array(
      'type' => 'varchar(8)',
      'pkey' => true,
      'label' => '国家代码',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'ct_name' => array(
      'type' => 'varchar(20)',
      'required' => true,
      'label' => '国家名称',
      'is_title' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'ct_name_en' => array(
      'type' => 'varchar(50)',
      'required' => true,
      'label' => 'Country Name',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'ct_code' => array(
      'type' => 'varchar(8)',
      'required' => true,
      'label' => '国家区号',
      'in_list' => true,
      'default_in_list' => true,
    ),
  ),
  'comment' => '国家表',
);
