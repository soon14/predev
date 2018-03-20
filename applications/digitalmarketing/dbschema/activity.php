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
$db['activity'] = array(
    'columns'=>array(
        'activity_id' =>array(
            'type' =>'number',
            'required' =>true,
            'label'=>'ID',
            'pkey' => true,
            'extra'=>'auto_increment'
        ),
        'bn'=>array(
            'type'=>'varchar(20)',
            'required'=>true,
            'label' =>'互动营销编号',
            'in_list' =>true,
            'default_in_list'=>true
        ),
        'type'=>array(
            'type'=>array(
                '1' =>'幸运大转盘',
                '2' =>'水果机',
                '3' =>'刮刮卡',
                '4' =>'摇一摇',
            ),
            'required' =>true,
            'label' =>'营销类型',
            'in_list'=>true
        ),
        'title'=>array(
            'type'=>'varchar(100)',
            'required' =>true,
            'label' =>'活动名称',
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title'=>true
        ),
        'from_time'=>array(
            'type'=>'time',
            'label'=>'开始时间',
            'in_list' =>true,
            'default_in_list'=>true
        ),
        'to_time'=>array(
            'type'=>'time',
            'label'=>'结束时间',
            'in_list' =>true,
            'default_in_list'=>true
        ),
        'description'=>array(
            'type'=>'text',
            'label'=>'活动说明'
        ),
        'use_score'=>array(
            'type'=>'number',
            'label'=>'消耗积分',
            'default'=>0
        ),
        'member_lv'=>array(
            'type' =>'varchar(50)',
            'default'=>'',
            'required'=>true,
            'label'=>'允许参与的会员等级'
        ),
        'frequency_limit'=>array(
            'type'=>'number',
            'default'=>0,
            'label'=>'每个用户每天限制次数'
        ),
        'point'=>array(
            'type'=>'number',
            'required'=>true,
            'label' =>'中奖概率'
        ),
        'partin_nums'=>array(
            'type'=>'number',
            'default'=>0,
            'label'=>'参与人次',
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'win_nums'=>array(
            'type'=>'number',
            'default'=>0,
            'label'=>'中奖人次',
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'opt_id'=>array(
            'type'=>'table:users@desktop',
            'label' =>'营销管理员',
            'in_list' =>true,
            'default_in_list'=>true
        ),
        'status'=>array(
            'type'=>array(
                '0' =>'禁用',
                '1' =>'启用',
            ),
            'default'=>'1',
            'label' =>'活动状态',
        ),
        'createtime'=>array(
            'type'=>'time',
            'label'=>'创建时间',
            'in_list' =>true,
            'default_in_list'=>true
        )

    ),
    'comment'=>'营销活动表'
);
