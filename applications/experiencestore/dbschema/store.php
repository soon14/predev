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


$db['store'] = array(
  'columns' => array(
    'id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'STORE_ID',
    ),
    'name' => array(
      'type' => 'varchar(200)',
      'label' => '名称',
      'is_title' => true,
      'required' => true,
      'searchtype' => 'has',
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'region' => array(
      'type' => 'region',
      'label' => '所在地区',
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,

    ),
    'address' => array(
      'type' => 'varchar(255)',
      'label' => '详细地址',
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'waytogo' => array(
      'type' => 'text',
      'label' => '前往方法',
    ),
    'phone' => array(
      'type' => 'varchar(255)',
      'label' => '电话',
      'searchtype' => 'has',
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'hours' => array(
      'type' => 'varchar(200)',
      'label' => '开放时间',
      'searchtype' => 'has',
      'filtertype' => 'normal',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'lat' => array(
      'type' => 'varchar(100)',
      'label' => '地理坐标(纬度lat)',
      'in_list' => true,

    ),
    'lng' => array(
      'type' => 'varchar(100)',
      'label' => '地理坐标(经度lng)',
      'in_list' => true,

    ),
    'map_image_id' => array(
      'type' => 'varchar(32)',
      'label' => '地图快照',
    ),
    'gallery_default_image_id' => array(
      'type' => 'varchar(32)',
      'label' => '相册默认图',
    ),
    'setting' => array(
      'type' => 'serialize',
      'label' => '设置',
    ),
    'desc' => array(
      'type' => 'longtext',
      'label' => '详细介绍',
    ),
    'sort' => array(
      'type' => 'number',
      'label' => '排序',
      'default' => 0,
      'orderby' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'createtime' => array(
      'type' => 'time',
      'label' => '创建时间',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'lastmodify' => array(
      'type' => 'last_modify',
      'label' => '最后更新时间',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'disabled' => array(
      'type' => 'bool',
      'default' => 'false',
      'comment' => ('失效'),
      'in_list' => true,
      'default_in_list' => true,
    ),
  ),
  'index' => array(
    'ind_key' => array(
      'columns' => array(
        0 => 'disabled',
      ),
    ),
  ),
  'comment' => ('地点'),
);
