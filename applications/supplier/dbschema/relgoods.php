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


$db['relgoods'] = array(
  'columns' => array(
    'id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => ('ID'),
    ),
    'supplier_id' => array(
      'type' => 'table:supplier',
      'required' => true,
      'label' => '供应商',
    ),
    'product_id' => array(
      'type' => 'table:products@b2c',
      'required' => true,
      'label' => ('SKU货品ID'),
    ),
    'goods_id' => array(
      'type' => 'table:goods@b2c',
      'required' => true,
      'label' => ('商品ID'),
    ),
    'purchase_price' => array(
        'type' => 'money',
        'label' => ('进货价'),
    ) ,
    'last_modify' => array(
      'type' => 'last_modify',
      'label' => ('更新时间'),
      'in_list' => true,
      'orderby' => true,
      'default_in_list' => true,
    ),
  ),
  'index' => array(
    'ind_goods'=>array(
        'columns'=>array(
            0 =>'goods_id'
        ),
    ),
    'ind_product'=>array(
        'columns'=>array(
            0 =>'product_id'
        ),
    ),
    'ind_last_modify' => array(
      'columns' => array(
        0 => 'last_modify',
      ),
    ),
  ),
  'comment' => ('商品渠道价格表'),
);
