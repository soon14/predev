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


$db['members'] = array(
    'columns' => array(
        'member_id' => array(
            'type' => 'table:members@b2c',
            'required' => true,
            'pkey' => true,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => ('禁用时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '6',
        ),
    ),
    'engine' => 'innodb',
    'comment' => ('会员黑名单') ,
);
