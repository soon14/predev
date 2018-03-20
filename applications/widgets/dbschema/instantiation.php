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


$db['instantiation'] = array(
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
            'label' => ('实例名称') ,
            'is_title' => true,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
            'order' => '1',
        ) ,
        'widgets_id' => array(
            'type' => 'table:widgets',
            'default' => 0,
            'required' => true,
            'label' => ('板块ID') ,
        ) ,
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => ('更新时间') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
            'order' => '5',
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => ('创建时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '6',
        ) ,
        'status'=>array(
            'type' => array(
                '0' => ('有效') ,
                '1' => ('暂停') ,
            ),
            'default'=>'0',
            'label'=>'状态',
            'order' => '4',
        ),
        'data' =>
            array (
              'type' => 'serialize',
              'label' =>'板块数据序列化',
            ),
        'nums' =>array(
            'type' =>'number',
            'required' => false,
            'default' => 0,
            'label' => ('楼层数据量') ,
            'comment' => ('楼层数据量') ,
            'in_list' => true,
        ),
        'image' =>array(
            'type' => 'char(32)',
            'label' => '板块形象图',
            'in_list' => true,
        ),
        'background_image' =>array(
            'type' => 'char(32)',
            'label' => '板块背景图',
            'in_list' => true,
        ),
        'link' =>array(
            'type' => 'varchar(255)',
            'label' => '板块链接',
            'in_list' => true,
        )
    ) ,
    'index' => array(
        'ind_name' => array(
            'columns' => array(
                0 => 'name',
            ) ,
        ),
        'ind_createtime' => array(
            'columns' => array(
                0 => 'createtime',
            ),
        ),
        'ind_last' => array(
            'columns' => array(
                0 => 'last_modify',
            ),
        ),
    ) ,
    'engine' => 'innodb',
    'comment' => ('板块实例表') ,
);
