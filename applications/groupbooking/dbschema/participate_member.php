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

$db['participate_member'] = array(
    'columns' => array(
        'gb_id' => array(
            'type' => 'table:orders',
            'required' => true,
            'pkey' => true,
            'comment' => '团购订单',
        ),
        'member_id' => array(
            'type' => 'table:members@b2c',
            'label' => '用户名',
            'comment' => '会员id',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'activity_id' => array(
            'type' => 'table:activity',
            'label' => '拼团活动id',
        ),
        'status' => array(
            'type' => array(
                '0' => '进行中',
                '1' => '是',
                '2' => '否',
            ),
            'default' => '0',
            'label' => '是否成团',
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'createtime' => array(
            'label' => ('参与时间') ,
            'type' => 'time',
            'required' => true,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),

    )

);