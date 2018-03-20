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


$db['activity_ticket'] = array(
  'columns' => array(
    'id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ticket_id',
    ),
    'batch_no' => array(
      'type' => 'varchar(100)',
      'label' => '票券批次号',
      'searchtype' => 'has',
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'name' => array(
      'type' => 'varchar(255)',
      'label' => '票券名称',
      'required' => true,
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'schedule_id' => array(
      'type' => 'table:activity_schedule',
      'label' => '场次',
      'required' => true,
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'price' => array(
      'type' => 'money',
      'label' => '价格',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
      'max' => array(
          'type' => 'number',
          'label' => '最多可售',
          'filtertype' => 'normal',
          'in_list' => true,
      ),
      'sale_nums' => array(
          'type' => 'number',
          'label' => '已售卖数量',
          'in_list' => true,
      ),

    'intro' => array(
      'type' =>'text',
      'label' => '票券说明',
      'filtertype' => 'normal',
      'in_list' => true,
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
  'comment' => ('活动票券/入场券'),
);
