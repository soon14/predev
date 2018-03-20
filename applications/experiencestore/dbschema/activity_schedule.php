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


$db['activity_schedule'] = array(
  'columns' => array(
    'id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'schedule_id',
    ),
    'code' => array(
      'type' => 'varchar(100)',
      'label' => '活动场次编码',
      'searchtype' => 'has',
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'subject_id' => array(
      'type' => 'table:activity_subject',
      'label' => '活动主题',
      'required' => true,
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'store_id' => array(
      'type' => 'table:store',
      'label' => '活动位置',
      'required' => true,
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'limit' => array(
      'type' => 'number',
      'label' => '活动人数限定',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'is_pub' => array(
      'type' =>'bool',
      'label' => '是否发布',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'need_ticket' => array(
      'type' =>'bool',
      'label' => '是否需要票券',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'from_time' => array(
      'type' => 'time',
      'label' => '活动开始时间',
      'filtertype' => 'normal',
      'in_list' => true,
        'orderby' => true,
        'default_in_list' => true,
    ),
    'to_time' => array(
      'type' => 'time',
      'label' => '活动结束时间',
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
    ),
      'status' =>array(
          'type' =>array(
              'sign' =>'报名中',
              'active' =>'活动中',
              'finish' =>'已结束'
          ),
          'default' =>'sign'
      ),
      'begin_time' => array(
          'type' => 'time',
          'label' => '预约开始时间',
          'in_list' => true,
      ),
      'end_time' => array(
          'type' => 'time',
          'label' => '预约结束时间',
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
  'comment' => ('活动场次表'),
);
