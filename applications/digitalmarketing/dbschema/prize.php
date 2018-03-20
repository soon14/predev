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

$db['prize'] =array(
    'columns'=>array(
        'prize_id'=>array(
            'type'=>'number',
            'pkey'=>'true',
            'required'=>'true',
            'extra'=>'auto_increment',
            'comment'=>'id'
        ),
        'activity_id'=>array(
            'type'=>'table:activity',
            'required'=>true,
            'comment' =>'营销活动id'
        ),
        'prize_grade'=>array(
            'type'=>array(
                '1'=>'一等奖',
                '2'=>'二等奖',
                '3'=>'三等奖',
                '4'=>'普通奖'
            ),
            'required'=>true,
            'comment' =>'奖品等级'
        ),
        'prize_title'=>array(
            'type'=>'varchar(50)',
            'comment' =>'奖品名称'
        ),
        'prize_type'=>array(
            'type'=>array(
                'coupon'=>'优惠券',
                'product'=>'商品',
                'score'=>'积分'
            ),
            'required' =>true,
            'comment'=>'奖品类型'
        ),
        'nums'=>array(
            'type'=>'number',
            'required'=>true,
            'default'=>0,
            'comment'=>'奖品数量'
        ),
        'win_nums'=>array(
            'type'=>'number',
            'default'=>0,
            'comment'=>'已中奖数量'
        ),
        'desc'=>array(
            'type'=>'text',
            'comment'=>'中奖结果说明'
        ),
        'addon'=>array(
            'type'=>'serialize',
            'comment'=>'奖品信息，如商品id ，优惠券id等'
        )
    ),
    'comment'=>'营销活动奖品表'
);
