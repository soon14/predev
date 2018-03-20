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


$db['currency'] = array(
  'columns' => array(
    'cur_id' => array(
      'type' => 'int(8)',
      'required' => true,
      'pkey' => true,
      'label' => '货币ID',

      'extra' => 'auto_increment',
      'in_list' => false,
    ),
    'cur_name' => array(
      'type' => 'varchar(20)',
      'required' => true,
      'default' => '',
      'label' => '货币名称',
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'cur_sign' => array(
      'type' => 'varchar(5)',
      'label' => '货币符号',
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'cur_code' => array(
      'type' => 'varchar(8)',
      'required' => true,
      'default' => '',
      'label' => '货币代码',

      'in_list' => true,
      'is_title' => true,
      'default_in_list' => true,
    ),

    'cur_rate' => array(
      'type' => 'decimal(10,4)',
      'default' => '1.0000',
      'required' => true,
      'label' => '汇率',
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'cur_default' => array(
      'type' => 'bool',
      'default' => 'false',
      'required' => true,
      'label' => '默认货币',
      'in_list' => true,
      'default_in_list' => true,
    ),
  ),

  'index' => array(
    'uni_ident_type' => array(
      'columns' => array(
        0 => 'cur_code',
      ),
      'prefix' => 'UNIQUE',
    ),
  ),
  'version' => '$Rev: 40654 $',
  'comment' => '货币表',
);
