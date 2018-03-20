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


$db['reserve'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ) ,
        'sale_id' => array(
            'type' => 'table:sales@sale',
            'label' => ('预约活动') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'member_id' => array(
            'type' => 'table:members@b2c',
            'label' => ('会员用户名') ,
            'filtertype' => 'yes',
            'searchtype'=>'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'tel' => array(
            'type' => 'varchar(50)',
            'required' => true,
            'default' => '',
            'label' => ('电话') ,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'goods_id' => array(
            'type' => 'table:goods@b2c',
            'default' => 0,
            'required' => true,
            'label' => ('商品ID') ,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'status'=>array(
            'type' => array(
                '0' => ('未购买') ,
                '1' => ('已购买') ,
            ),
            'default'=>'0',
            'label'=>'购买状态',
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '4',
        ),
        'number' => array(
            'type' => 'number',
            'required' => true,
            'label'=>'购买数量',
            'default'=>0,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => ('预约时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '6',
        ),
    ),
    'index' => array(
        'ind_name' => array(
            'columns' => array(
                0 => 'tel',
            ) ,
        ),
        'ind_time' => array(
            'columns' => array(
                0 => 'createtime',
            ),
        ),
    ) ,
    'engine' => 'innodb',
    'comment' => ('用户预约表') ,
);
