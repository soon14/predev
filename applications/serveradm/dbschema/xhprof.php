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


 
$db['xhprof']=array (
  'columns' =>
  array (
    'run_id' =>
    array (
      'required' => true,
      'pkey' => true,
      'type' => 'varchar(100)',
      'label' => 'run_id',
      'width' => 80,
      'is_title' => true,
      'required' => true,
      'comment' => 'run_id',
      
      //'searchtype' => 'has',
      'in_list' => false,
      'default_in_list' => false,
    ),
    'source' =>
    array (
      'type' => 'varchar(50)',
      'label' => 'source',
      'width' => 350,
      'comment' => 'source',
      
      //'searchtype' => 'has',
      'in_list' => false,
      'default_in_list' => false,
    ),
    'app' =>
    array (
      'type' => 'varchar(30)',
      'comment' => 'app',
      'width' => 80,
      
      'label' => 'app',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'ctl' =>
    array (
      'type' => 'varchar(100)',
      'comment' => 'controller',
      'width' => 80,
      
      'label' => 'controller',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'act' =>
    array (
      'type' => 'varchar(50)',
      'label' => 'action',
      'width' => 80,
      'comment' => 'action',
      
      //'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'request_uri' =>
    array (
      'type' => 'varchar(255)',
      'label' => 'request_uri',
      'width' => 300,
      'comment' => 'request_uri',
      
      //'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'params' =>
    array(
        'type' => 'serialize',
        'label' => 'params',
        'deny_export' => true,
    ),
    'addtime' =>
    array(
      'type' => 'last_modify',
      'label' => 'addtime',
      'width' => 130,
      
      'in_list' => true,
      'default_in_list' => true,
    ),
    'wt' => 
    array (
      'type' => 'int(10) unsigned',
      'label' => 'Wall Time',
      'required' => false,
      
      'width' => 80,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'mu' => 
    array (
      'type' => 'int(10) unsigned',
      'label' => 'Memory Used',
      'required' => false,
      
      'width' => 80,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'pmu' => 
    array (
      'type' => 'int(10) unsigned',
      'label' => 'PeakMemUse',
      'required' => false,
      
      'width' => 80,
      'in_list' => true,
      'default_in_list' => true,
    ),
  ),
  'comment' => 'xphrof',
  'version' => '$Rev: 40654 $',
);
