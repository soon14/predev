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



$db['activities'] = array (
    'columns' => array (
        'id' => array (
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => ('ID'),
            'comment' => ('ID'),
        ),
        'activity_id' => array (
            'type' => 'table:activity',
            'required' => true,
            'label' => ('活动'),
            'comment' => ('活动ID'),
        ),
        'cpns_id' => array (
            'type' => 'table:coupons@b2c',
            'required' => true,
            'label' => ('优惠券'),
            'comment' => ('优惠券ID'),
        ),
        'num' => array(
            'type' => 'number',
            'default' => 0,
            'label'=> ('活动单人可领取数量'),
            'comment' => ('活动单人可领取数量'),
        ),
        'num_sum' => array(
            'type' =>'number',
            'required' => true,
            'default' => 0,
            'label'=> ('活动共可领取数量'),
            'comment' => ('活动共可领取数量'),
        ),
        'achieve_sum' => array(
            'type' =>'number',
            'required' => true,
            'default' => 0,
            'label'=> ('已领取数量'),
            'comment' => ('已领取数量'),
        ),
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => ('最后更新时间'),
            'comment' => ('最后更新时间'),
        ),
    ),

    'index' => array (
        'uni_cpns_activity_id' => array(
            'columns' => array(
                0 => 'activity_id',
                1 => 'cpns_id',
            ) ,
            'prefix' => 'UNIQUE',
        ) ,
        'ind_num' => array (
            'columns' => array (
                0 => 'num',
            ),
        ),
        'ind_num_sum' => array (
            'columns' => array (
                0 => 'num_sum',
            ),
        ),
    ),

    'comment' => ('优惠券活动关联表'),
    'engine' => 'innodb',
);
