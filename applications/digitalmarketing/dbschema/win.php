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


$db['win'] = array(
    'columns' => array(
        'win_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra'=>'auto_increment',
        ) ,
        'partin_id' => array(
            'type' => 'table:partin',
            'label' => ('参与记录') ,
            'comment' =>('参与记录Id'),
        ) ,
        'prize_id' => array(
            'type' => 'table:prize',
            'label' => ('奖品') ,
            'comment' =>('奖品Id'),
        ) ,
        'prize_type'=>array(
            'type'=>array(
                'coupon'=>'优惠券',
                'product'=>'商品',
                'score'=>'积分'
            ),
            'required' =>true,
            'in_list' => true,
            'label'=>'奖品类型'
        ),
        'prize_detail'=>array(
            'type'=>'serialize',
            'label' => ('奖品') ,
            'comment' =>('奖品'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'order_id'=>array(
            'type'=>'table:orders@b2c',
            'label' => ('关联订单') ,
            'comment' =>('关联订单'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'activity_id' => array(
            'type' => 'table:activity',
            'label' => ('活动') ,
            'comment' =>('活动Id'),
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'member_id' => array(
            'type' => 'table:members@b2c',
            'label' => ('会员') ,
            'comment' =>('会员Id'),
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => ('创建时间') ,
            'in_list' => true,
            'default_in_list' => true,
            'orderby' =>true
        ) ,
        'last_modify' => array(
            'label' => ('最后更新时间') ,
            'type' => 'last_modify',
            'in_list' => true,
            'orderby' =>true
        ) ,

    ) ,
    'engine' => 'innodb',
    'label' => ('中奖记录表') ,
);
