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
$db['rule'] = array(
    'columns' => array(
        'rule_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => ('id'),
            'comment' => ('id'),
        ) ,
        'cpns_id' => array(
            'type' => 'table:coupons@b2c',
            'required' => true,
            'default' => 0,
            'comment' => ('优惠券id') ,
            'label' => '优惠券',
            'in_list' =>true,
            'default_in_list' =>true
        ) ,
        'from_time' =>
            array (
                'type' => 'time',
                'label' => ('起始时间'),
                'default'=> 0,
                'editable' => true,
                'in_list' => true,
                'default_in_list' => true,
                'filterdefault'=>true,
            ),
        'to_time' =>
            array (
                'type' => 'time',
                'label' => ('截止时间'),
                'default'=> 0,
                'editable' => true,
                'in_list' => true,
                'default_in_list' => false,
                'filterdefault'=>true,
            ),

        'rule_status' =>array(
            'type'=>array(
                '0' =>'禁用',
                '1' =>'启用'
            ),
            'required' =>true,
            'default' => '0'
        )
    )
);