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


$db['reldelivery'] = array(
  'columns' => array(
    'delivery_id' => array(
      'type' => 'table:delivery@b2c',
      'required' => true,
      'pkey' => true,
      'label' => ('delivery_id'),
    ),
    'shop_id' => array(
      'type' => 'table:shop',
      'required' => true,
      'label' => ('微店铺ID'),
    ),
    'shop_name' => array(
      'type' => 'varchar(50)',
      'required' => true,
      'label' => '微店铺名称',
    ),
  ),
  'index' => array(
    'ind_shop' => array(
      'columns' => array(
        0 => 'shop_id',
      ),
    ),
    'ind_shop_name' => array(
      'columns' => array(
        0 => 'shop_name',
      ),
    ),
  ),
  'comment' => ('微店铺与发货单关联表'),
);
