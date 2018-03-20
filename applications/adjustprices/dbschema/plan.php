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


$db['plan'] = array(
  'columns' => array(
    'plan_id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
    ),
    'plan_name' => array(
      'type' => 'varchar(100)',
      'label' => '活动名称',
      'is_title' => true,
      'required' => true,//是否必填
      'searchtype' => 'has',//是否支持搜索
      'filtertype' => 'normal',//支持筛选
      'in_list' => true,//是否可在管理后台列表UI展现
      'default_in_list' => true,//是否默认展现
      'orderby' => true,//支持排序
    ),
    'plan_title' => array(
      'type' => 'text',
      'label' => '活动描述',
      'in_list' => true,//是否可在管理后台列表UI展现
    ),
    'product_count' => array(
      'type' => 'number',
      'label' => '限时降价SKU货品数',
      'required' => true,//是否必填
      'default' => 0,
      'filtertype' => 'normal',//支持筛选
      'in_list' => true,//是否可在管理后台列表UI展现
      'default_in_list' => true,//是否默认展现
      'orderby' => true,//支持排序
    ),
    'carry_out_time' => array(
      'type' => 'time',
      'label' => '降价执行时间点',
      'filtertype' => 'normal',//支持筛选
      'in_list' => true,//是否可在管理后台列表UI展现
      'default_in_list' => true,//是否默认展现
      'orderby' => true,//支持排序
    ),
    'rollback_time' => array(
      'type' => 'time',
      'label' => '恢复原价时间点',
      'filtertype' => 'normal',//支持筛选
      'in_list' => true,//是否可在管理后台列表UI展现
      'default_in_list' => true,//是否默认展现
      'orderby' => true,//支持排序
    ),
    'plan_status' => array(
      'type' => array(
          '0' => '准备中',
          '1' => '确认按计划执行',
          '2' => '降价任务处理中' ,
          '3' => '恢复价格中' ,
          '4' => '已执行降价' ,
          '5' => '已恢复原价' ,
      ) ,
      'label' => '状态',
      'filtertype' => 'normal',//支持筛选
      'in_list' => true,//是否可在管理后台列表UI展现
      'default_in_list' => true,//是否默认展现
      'orderby' => true,//支持排序
    ),
    'createtime' => array(
      'type' => 'time',
      'label' => '首次创建时间',
      'filtertype' => 'normal',//支持筛选
      'in_list' => true,//是否可在管理后台列表UI展现
      'default_in_list' => true,//是否默认展现
      'orderby' => true,//支持排序
    ),
    'last_modify' => array(
      'type' => 'last_modify',
      'label' => '最后执行时间',
      'filtertype' => 'normal',//支持筛选
      'in_list' => true,//是否可在管理后台列表UI展现
      'default_in_list' => true,//是否默认展现
      'orderby' => true,//支持排序
    ),
  ),
  'comment' => ('降价活动表'),
);
