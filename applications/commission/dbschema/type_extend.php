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
$db['type_extend'] = array(
    'columns' => array(
        'type_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => '关联goods_type',
            'pkey' => true,
            'label' => ('商品类型ID'),
            'in_list' => true,
        ),
        'name' => array(
            'type' => 'varchar(100)',
            'required' => true,
            'default' => '',
            'label' => ('类型名称'),
            'is_title' => true,
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
            'order' => '1',
        ),
        'commission_value' => array(
            'type' => 'serialize',
            'comment' => ('分佣比例或金额array(first => value1 ,second=>value2)')
        ),
        'lv_commission_value' => array(
            'type' => 'serialize',
            'comment' => ('分佣比例或金额array(lv0 => value0 ,lv1=>value1 ...)')
        ),
    )
);