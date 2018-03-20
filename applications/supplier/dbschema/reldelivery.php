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
    'supplier_id' => array(
      'type' => 'table:supplier',
      'required' => true,
      'label' => ('供应商'),
    ),
    'supplier_bn' => array(
      'type' => 'varchar(20)',
      'required' => true,
      'label' => '供应商编号',
    ),
  ),
  'index' => array(
    'ind_supplier' => array(
      'columns' => array(
        0 => 'supplier_id',
      ),
    ),
    'ind_supplier_bn' => array(
      'columns' => array(
        0 => 'supplier_bn',
      ),
    ),
  ),
  'comment' => ('供应商与发货单关联表'),
);
