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



$db['activity'] = array (
    'columns' => array (
        'activity_id' => array (
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => ('优惠券活动'),
            'comment' => ('优惠券活动ID'),
        ),
        'name' => array (
            'type' => 'varchar(255)',
            'searchtype' => 'has',
            'label' => ('活动名称'),
            'comment' => ('活动名称'),
            'in_list' => true,
            'default_in_list' => true,
            'is_title' => true,
        ),
        'brief' => array(
            'type' => 'varchar(255)',
            'label' => ('活动简介') ,
            'comment' => ('活动简介'),
            'filtertype' => 'normal',
            'in_list' => true,
        ),
        'image_id' => array (
            'type' => 'varchar(32)',
            'label' => ('活动图') ,
            'comment' => ('活动图ID'),
        ),
        'status' => array (
            'type' => 'bool',
            'label' => ('是否启用'),
            'comment' => ('是否启用'),
            'filtertype' => 'yes',
            'required' => true,
            'default' => 'false',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'from_time' => array (
            'type' => 'time',
            'label' => ('开始时间'),
            'comment' => ('开始时间'),
            'orderby'=> true,
            'filtertype' => 'time',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'to_time' => array (
            'type' => 'time',
            'label' => ('结束时间'),
            'comment' => ('结束时间'),
            'orderby'=> true,
            'filtertype' => 'time',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'op_id' => array (
            'type' => 'varchar(255)',
            'default' => '',
            'label' => ('操作者'),
            'comment' => ('操作者ID'),
        ),
        'op_name' => array (
            'type' => 'varchar(255)',
            'default' => '',
            'label' => ('操作者'),
            'comment' => ('操作者'),
            'in_list' => true,
        ),
        'op_time' => array (
            'type' => 'time',
            'label' => ('启用时间'),
            'comment' => ('启用时间'),
        ),
        'createtime' => array (
            'type' => 'time',
            'label' => ('添加时间'),
            'comment' => ('添加时间'),
            'orderby'=> true,
            'filtertype' => 'time',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => ('最后更新时间'),
            'comment' => ('最后更新时间'),
            'in_list' => true,
            'orderby'=> true,
            'default_in_list' => true,
        ),
    ),

    'index' => array (
        'ind_from_time' => array (
            'columns' => array (
                0 => 'from_time',
            ),
        ),
        'ind_to_time' => array (
            'columns' => array (
                0 => 'to_time',
            ),
        ),
    ),

    'comment' => ('优惠券活动主表'),
    'engine' => 'innodb',
);
