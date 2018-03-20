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


$db['partin'] = array(
    'columns' => array(
        'partin_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra'=>'auto_increment',
        ) ,
        'prize_id' => array(
            'type' => 'table:prize',
            'label' => ('奖品id') ,
            'comment' =>('奖品Id'),
        ) ,
        'activity_id' => array(
            'type' => 'table:activity',
            'label' => ('活动') ,
            'comment' =>('活动Id'),
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'is_win' => array(
            'type' => array(
                '0' => ('否') ,
                '1' => ('是') ,
            ) ,
            'required' => true,
            'label' => ('是否中奖') ,
            'comment' =>('是否中奖'),
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' =>'yes'
        ) ,
        'status' => array(
            'type' => array(
                '0' => ('未发放') ,
                '1' => ('已发放') ,
            ) ,
            'default'=>'0',
            'required' => true,
            'label' => ('奖品发放状态')
        ) ,
        'member_id' => array(
            'type' => 'table:members@b2c',
            'label' => ('会员') ,
            'comment' =>('会员Id'),
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'remarks' => array(
            'type' => 'varchar(255)',
            'label' => ('备注') ,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => ('参与时间') ,
            'in_list' => true,
            'default_in_list' => true,
            'orderby' =>true
        ) ,

    ) ,
    'engine' => 'innodb',
    'label' => ('参与记录表') ,
);
