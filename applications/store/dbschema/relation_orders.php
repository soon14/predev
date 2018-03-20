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

$db['relation_orders'] = array(
    'columns' => array(
        'order_id' => array(
            'pkey' => true,
            'type'     => 'bigint unsigned',
            'required' => true,
            'comment'  => ('订单id'),
        ),
        'store_id' => array(
            'type'     => 'table:store',
            'required' => true,
            'default'  => '0',
            'in_list' => true,
            'default_in_list' => true,
            'label' =>'店铺',
            'comment'  => ('店铺id'),
        ),
        'op_id' => array(
            'type' =>'table:users@desktop',
            'label' =>'操作员',
            'in_list' => true,
            'default_in_list' => true,
            'comment'  => ('操作员id'),
        ),
        'op_no' => array(
            'type' => 'varchar(50)',
            'label' => '工号',
            'in_list' => true,
            'default_in_list' => true,
            'comment' => '工号',
        )
    ),
    'engine'  => 'innodb',
    'comment' => ('订单店铺关联表'),
);
