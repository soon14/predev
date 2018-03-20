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


$db['products'] = array(
    'columns' => array(
        'res_id' => array(
            'type' => 'table:restrict',
            'required' => true,
            'label' => ('限购ID'),
        ),
        'product_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => ('商品ID'),
        ),
        'day_times_limit' => array(
            'type' => 'int(10) unsigned',
            'default' => 0,
            'label' => ('用户每天最多购买次数'),
            'comment' => ('用户每天最多购买次数'),
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'day_member_limit' => array(
            'type' => 'int(10) unsigned',
            'default' => 0,
            'label' => ('用户每天最多购买数量'),
            'comment' => ('用户每天最多购买数量'),
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'order_limit' => array(
            'type' => 'int(10) unsigned',
            'default' => 0,
            'label' => ('订单最多购买数量'),
            'comment' => ('订单最多购买数量'),
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'member_limit' => array(
            'type' => 'int(10) unsigned',
            'default' => 0,
            'label' => ('用户最多购买数量'),
            'comment' => ('用户最多购买数量'),
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'sum' => array(
            'type' => 'int(10) unsigned',
            'default' => 0,
            'label' => ('总数量'),
            'comment' => ('总数量'),
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
    ),

    'comment' => ('限购商品集合表'),
);
