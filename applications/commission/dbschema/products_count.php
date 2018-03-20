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
$db['products_count'] = array(
    'columns' => array(
        'product_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => '关联products',
            'in_list' => true,
            'pkey' => true,
        ),
        'commission_total' => array(
            'type' => 'money',
            'label' => ('分佣总金额'),
            'required' => true,
            'comment' => ('分佣总金额'),
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        ),
        'commission_settle' => array(
            'type' => 'money',
            'label' => ('商品已结算分佣'),
            'required' => true,
            'default' => 0,
            'comment' => ('商品已结算分佣'),
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        )
    )
);