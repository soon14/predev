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


$db['subject'] = array(
  'columns' => array(
    'id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'id',
    ),
    'goods_id' => array(
      'type' => 'table:goods@b2c',
      'label' => '商品',
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'goods_discount'=>array(
      'type' => 'money',
      'label' => '商品优惠',
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'fg_title' => array(
      'type' => 'varchar(255)',
      'label' => '标题',
      'is_title' => true,
      'required' => true,
      'searchtype' => 'has',
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'fg_intro' => array(
      'type' => 'text',
      'label' => '简介',
      'searchtype' => 'has',
      'filtertype' => 'normal',
      'in_list' => true,
    ),
    'fg_desc' => array(
      'type' => 'longtext',
      'label' => '详细介绍',
    ),
    'limit' => array(
      'type' => 'number',
      'label' => '可购数量',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'per_limit' => array(
      'type' => 'number',
      'label' => '每人限购',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'setting' => array(
      'type' => 'serialize',
      'label' => '设置',
    ),
    'sort' => array(
      'type' => 'number',
      'label' => '排序',
      'default'=>0,
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'customer_memo_title' => array(
      'type' => 'text',
      'label' => '顾客备注标题',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'customer_memo_options_enabled' => array(
      'type' => 'text',
      'label' => '启用顾客备注选项',
      'in_list' => true,
    ),
    'customer_memo_options' => array(
      'type' => 'text',
      'label' => '顾客备注选项',
      'in_list' => true,
    ),
    'customer_memo_options_multiple' => array(
      'type' => 'bool',
      'label' => '备注选项支持多选',
      'in_list' => true,
    ),
    'op_memo' => array(
      'type' => 'text',
      'label' => '管理员活动备注',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'begin_time' => array(
      'type' => 'time',
      'label' => '活动开始时间',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'end_time' => array(
      'type' => 'time',
      'label' => '活动结束时间',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'is_pub' => array(
      'type' => 'bool',
      'label' => '活动是否发布',
      'default'=>'true',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'createtime' => array(
      'type' => 'time',
      'label' => '创建时间',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'lastmodify' => array(
      'type' => 'last_modify',
      'label' => '最后更新时间',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
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
  'comment' => ('快团活动主题表'),
);
