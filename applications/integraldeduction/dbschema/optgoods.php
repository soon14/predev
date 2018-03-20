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


$db['optgoods'] = array(
  'columns' => array(
    'goods_id' => array(
      'type' => 'table:goods@b2c',
      'pkey' => true,
      'required' => true,
      'label' => ('商品ID'),
    ),
    'scale' => array(
      'type' => 'float(6,3)',
      'required' => true,
      'default'=>1,
      'label' => ('可兑换比例'),
    ),
    'lock_scale'=>array(
        'type'=>'bool',
        'required'=>true,
        'default'=>'false',
        'label'=>'是否锁定比例'
    )
  ),

  'comment' => ('商品可抵扣比例表'),
);
