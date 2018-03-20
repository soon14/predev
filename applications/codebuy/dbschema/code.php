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


$db['code'] = array(
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
        'code' => array(
            'type' => 'varchar(200)',
            'required' => true,
            'default' => '',
            'label' => ('优购码') ,
            'is_title' => true,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
            'order' => '1',
        ),
        'op_name' => array(
            'type' => 'varchar(255)',
            'required' => true,
            'label' => ('操作员') ,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'status'=>array(
            'type' => array(
                '0' => ('未使用') ,
                '1' => ('已经使用') ,
            ),
            'default'=>'0',
            'label'=>'是否使用',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'remark' => array(
            'type' => 'text',
            'required' => true,
            'default' => '',
            'label' => ('发行备注') ,
            'searchtype' => 'has',
        ),
        'createtime' => array(
            'type' => 'time',
            'label' => ('发行时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '6',
        ),
    ),
    'index' => array(
        'ind_activity' => array(
            'columns' => array(
                0 => 'activity_id',
            ) ,
        ),
        'ind_time' => array(
            'columns' => array(
                0 => 'createtime',
            ),
        ),
        'ind_code' => array(
            'columns' => array(
                0 => 'code',
            ),
        ),
    ) ,
    'engine' => 'innodb',
    'comment' => ('优购码列表') ,
);
