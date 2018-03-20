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



$db['achieve'] = array (
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
            'in_list' => true,
            'default_in_list' => true,
        ),
        'cpns_id' => array (
            'type' => 'table:coupons@b2c',
            'required' => true,
            'label' => ('优惠券'),
            'comment' => ('优惠券ID'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'member_id' => array (
            'type' => 'table:members@b2c',
            'required' => true,
            'default' => 0,
            'label' => ('用户'),
            'comment' => ('用户ID'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'num' => array(
            'type' => 'number',
            'required' => true,
            'default' => 0,
            'label'=> ('领取数量'),
            'comment' => ('领取数量'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'mobile' => array(
            'type' => 'varchar(50)',
            'label' => ('手机') ,
            'comment' => ('用户手机'),
            'searchtype' => 'head',
            'editable' => true,
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'in_list' => true,
        ) ,
        'createtime' => array (
            'type' => 'time',
            'label' => ('创建时间'),
            'comment' => ('创建时间'),
            'orderby'=> true,
            'filtertype' => 'time',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => ('更新时间'),
            'comment' => ('更新时间'),
            'in_list' => true,
            'orderby'=> true,
            'default_in_list' => true,
        ),
    ),

    'index' => array (
        'uni_member_cpns_id' => array(
            'columns' => array(
                0 => 'activity_id',
                1 => 'cpns_id',
                2 => 'member_id',
            ) ,
            'prefix' => 'UNIQUE',
        ) ,
        'ind_createtime' => array (
            'columns' => array (
                0 => 'createtime',
            ),
        ),
    ),
    'comment' => ('优惠券活动领取记录表'),
    'engine' => 'innodb',
);
