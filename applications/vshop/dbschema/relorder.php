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


$db['relorder'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'ID' ,
            'pkey' => true,
            'extra' => 'auto_increment',
        ) ,
        'order_id' => array(
            'type' => 'table:orders@b2c',
            'required' => true,
            'label' => '订单号',
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'shop_id' => array(
            'type' => 'table:shop',
            'required' => true,
            'label' => '微店铺ID',
            'filtertype' => 'yes',
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'shop_name' => array(
            'type' => 'varchar(50)',
            'label' => '微店铺名称' ,
            'filtertype' => 'yes',
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'comment' => ('单据创建时间') ,
            'label' => ('单据创建时间') ,
            'filtertype' => 'yes',
            'orderby' => true,
            'in_list' => true,
        ) ,
        'memo' => array(
            'type' => 'text',
            'label' => '备注' ,
            'comment' => '备注' ,
        ) ,
        'disabled' => array(
            'type' => 'bool',
            'default' => 'false',
            'comment' => ('是否无效') ,
        ) ,
    ),
    'comment' => ('微店订单表') ,
);
