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


$db['users'] = array(
    'columns' => array(
        'user_id' => array(
            'type' => 'number',
            'extra' => 'auto_increment',
            'pkey' => true,
            'label' => '社区用户ID',
        ),
        'user_lv_id' => array(
            'required' => true,
            'default' => 0,
            'label' => ('用户等级') ,
            'type' => 'table:user_lv',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'member_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '会员账号id',
            'in_list' => true,
            'default_in_list' => true
        ),
        'nickname' => array(
            'type' => 'varchar(32)',
            'label' => '昵称' ,
            'searchtype' => 'has',
            'is_title'=>true,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'sign' => array(
            'type' => 'varchar(255)',
            'label' => '签名',
            'default' => '',
            'in_list' => true,
            'default_in_list' => false,
        ),
        'follow_count' => array(
            'type' => 'number',
            'label' => '粉丝数',
            'default' => 0,
            'in_list' => true,
            'default_in_list' => false,
        ),
        'mark' => array(
            'type' => 'text',
            'label' => '备注'
        ),
    ) ,
    'index' => array(
        'user' => array(
            'columns' => array(
                'user_id',
                'member_id',
            ) ,
            'prefix' => 'UNIQUE',
        ),
    ) ,
    'engine' => 'innodb',
    'comment' => ('社区用户主表') ,
);
