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


$db['data_log'] = array(

    'columns' => array(
        'log_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ) ,
        'type' => array(
            'type' => array(
                'instantiation' => ('实例') ,
                'widgets' =>'板块',
            ),
            'label' => ('类型') ,
            'required'=>'true',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'target_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'label' => '数据ID',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'data' => array(
            'type' => 'serialize',
            'label' => ('数据') ,
        ),
        'name' => array(
            'type' => 'varchar(200)',
            'required' => true,
            'default' => '',
            'label' => ('名称') ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => ('创建时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '7',
        ) ,
    ) ,
    'index' => array(
        'ind_target_id' => array(
            'columns' => array(
                0 => 'target_id',
            ) ,
        )
    ) ,
    'engine' => 'innodb',
    'comment' => ('板块记录表') ,
);
