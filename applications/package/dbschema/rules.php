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


$db['rules'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ) ,
        'package_id' => array(
            'type' => 'table:package',
            'required' => true,
            'label' => ('组合套餐活动ID') ,
        ) ,
        'name' => array(
            'type' => 'varchar(200)',
            'required' => true,
            'default' => '',
            'label' => ('组合套餐活动名称') ,
            'is_title' => true,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
            'order' => '1',
        ),
        'intro' => array(
            'type' => 'text',
            'required' => true,
            'default' => '',
            'label' => ('组合套餐活动说明') ,
            'searchtype' => 'has',
        ),
        'goods_id' => array(
            'type' => 'table:goods@b2c',
            'default' => 0,
            'required' => true,
            'label' => ('参与活动商品') ,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'package_goods' => array(
            'type' => 'serialize',
            'default' => 0,
            'required' => true,
            'label' => ('组合套餐商品') ,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'status'=>array(
            'type' => array(
                '0' => ('有效') ,
                '1' => ('暂停') ,
            ),
            'default'=>'0',
            'label'=>'是否启用',
            'order' => '4',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'start' => array(
            'type' => 'time',
            'label' => ('开始时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '6',
        ),
        'end' => array(
            'type' => 'time',
            'label' => ('结束时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '6',
        ),
    ),
    'index' => array(
        'ind_name' => array(
            'columns' => array(
                0 => 'name',
            ) ,
        ),

        'ind_time' => array(
            'columns' => array(
                0 => 'start',
                1 => 'end',
            ),
        ),
    ) ,
    'engine' => 'innodb',
    'comment' => ('组合套餐规则表') ,
);
