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


$db['hello'] = array(
  'columns' => array(
    'hello_id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
    ),
    'label1' => array(
      'type' => 'varchar(50)',
    //   'type' => 'bool',
    //   'type' => 'number',
    //   'type' => 'int',
    //   'type' => 'serialize',#序列化类型
    //   'type' => 'last_modify',
    //   'type' => 'longtext',
    //   'type' => 'text',
    //   'type' => 'money',#货币类型
    //   'type' => 'time',
    //   'type' => 'region',#地区类型
    //   'type' => 'int unsigned',
    //   'type' => 'smallint unsigned',
    //   'type' => 'float(6,3)',
    //   'type' =>  array(
    //       'eum1' => '枚举1',
    //       'eum2' => '枚举2',
    //       'eum3' => '枚举3' ,
    //   ) ,
      'label' => '字段1',
      'is_title' => true,
      'required' => true,//是否必填
      'searchtype' => 'has',//是否支持搜索
      'filtertype' => 'normal',//支持筛选
      'in_list' => true,//是否可在管理后台列表UI展现
      'default_in_list' => true,//是否默认展现
      'orderby' => true,//支持排序
    ),
  ),
  //索引
  'index' => array(
    'ind_key' => array(
      'columns' => array(
        0 => 'column',
      ),
    ),
  ),
  'comment' => ('表名备注'),
);
