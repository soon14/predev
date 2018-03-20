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
    'goods_id' => array(
      'type' => 'table:goods@b2c',
      'pkey' => true,
      'required' => true,
      'label' => '商品ID',
    ),
    'deduction' => array(
      'type' => 'number',
      'required' => true,
      'default'=>999999,
      'label' => '兑换消耗积分',
    ),
    'marketable'=>array(
        'type'=>'bool',
        'required'=>true,
        'default'=>'true',
        'label'=>'是否在积分商城上架'
    ),
    'popularity'=>array(
        'type'=>'int unsigned',
        'label'=>'人气（累计兑换次数)'
    )
  ),
  'comment' => '积分商城商品',
);
