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


$db['log'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ) ,
        'activity_id' => array(
            'type' => 'table:activity@codebuy',
            'label' => ('优购码活动') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'code_id' => array(
            'type' => 'table:code@codebuy',
            'label' => ('优购码') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'searchtype' => 'has',
            'default_in_list' => true,
        ),
        'member_id' => array(
            'type' => 'table:members@b2c',
            'label' => ('会员用户名') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'searchtype' => 'has',
            'default_in_list' => true,
        ),
        'order_id' => array(
            'type' => 'table:orders@b2c',
            'required' => true,
            'default' => 0,
            'label' => ('订单号') ,
            'in_list' => true,
            'searchtype' => 'has',
            'default_in_list' => true,
        ),
        'usetime' => array(
            'type' => 'time',
            'label' => ('使用时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '6',
        ),
        'number' => array(
            'type' => 'number',
            'required' => true,
            'label'=>'购买数量',
            'default'=>0,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
    ),
    'index' => array(
        'ind_code' => array(
            'columns' => array(
                0 => 'code_id',
            ) ,
        ),
        'ind_member' => array(
            'columns' => array(
                0 => 'member_id',
            ) ,
        ),
        'ind_order' => array(
            'columns' => array(
                0 => 'order_id',
            ) ,
        ),
        'ind_time' => array(
            'columns' => array(
                0 => 'usetime',
            ),
        ),
    ) ,
    'engine' => 'innodb',
    'comment' => ('优购码使用日志') ,
);
