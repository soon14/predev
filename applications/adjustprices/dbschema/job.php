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


$db['job'] = array(
  'columns' => array(
    'plan_id' => array(
      'type' => 'table:plan',
      'required' => true,
      'pkey' => true,
      'label' => ('计划ID'),
    ),
    'product_id' => array(
      'type' => 'table:products@b2c',
      'pkey' => true,
      'required' => true,
      'label' => ('SKU货品ID'),
    ),
    'goods_id' => array(
      'type' => 'table:goods@b2c',
      'required' => true,
      'label' => ('商品ID'),
    ),
    'begin_price' => array(
        'type' => 'money',
        'label' => ('原销售价'),
    ),
    'end_price' => array(
        'type' => 'money',
        'label' => ('目标价格'),
    ),
  ),
    'index' => array(
        'ind_plan_id' => array(
            'columns' => array(
                0 => 'plan_id',
            ),
        ),
    ),
  'comment' => ('调价计划任务表'),
);
