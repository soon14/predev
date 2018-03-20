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


$db['sales'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ) ,
        'name' => array(
            'type' => 'varchar(200)',
            'required' => true,
            'default' => '',
            'label' => ('预售活动名称') ,
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
            'label' => ('预售活动说明') ,
            'searchtype' => 'has',
        ),
        'image' => array(
            'type' => 'varchar(32)',
            'label' => ('活动图片') ,
        ) ,
        'goods_id' => array(
            'type' => 'table:goods@b2c',
            'default' => 0,
            'required' => true,
            'label' => ('商品ID') ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'status'=>array(
            'type' => array(
                '0' => ('有效') ,
                '1' => ('暂停') ,
            ),
            'default'=>'0',
            'label'=>'状态',
            'order' => '4',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'reserve_start' => array(
            'type' => 'time',
            'label' => ('预约开始时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '6',
        ),
        'reserve_end' => array(
            'type' => 'time',
            'label' => ('预约结束时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '6',
        ),
        'start' => array(
            'type' => 'time',
            'label' => ('抢购开始时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '6',
        ),
        'end' => array(
            'type' => 'time',
            'label' => ('抢购结束时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '6',
        ),
        'alert' => array(
            'type' => 'time',
            'label' => ('开售提醒时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '6',
        ),
        'number' => array(
            'type' => 'number',
            'label' => ('每单限购数量') ,
            'in_list' => true,
            'default_in_list' => true,
            'default' => 1,
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
                2 => 'alert',
                3 => 'reserve_start',
                4 => 'reserve_end',
            ),
        ),
    ) ,
    'engine' => 'innodb',
    'comment' => ('预售列表') ,
);
