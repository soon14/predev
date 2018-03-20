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


$db['notice_integral'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => '公告积分记录ID',

        ),
        'notice_id' => array(
            'type' => 'table:notice',
            'required' => true,
            'label' => '公告ID',
        ),
        'member_id' => array(
            'type' => 'table:members@b2c',
            'required' => true,
            'default' => 0,
            'comment' => '会员ID',
        ),
        'time' => array(
            'type' => 'time',
            'label' => '赠送时间',
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'integral' => array(
            'type' => 'number',
            'label' => '积分',
            'in_list' => true,
            'comment' => '送的积分数'
        ),
    ),
    'comment' => '公告积分赠送记录',
);
