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

$db['group'] = array(
    'columns' => array(
        'group_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'ID',
            'pkey' => true,
            'extra' => 'auto_increment',
            'in_list' => true,
        ),
        'name' => array(
            'type' => 'varchar(255)',
            'required' => true,
            'default' => '',
            'label' => ('分组名称'),
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            'is_title' => true,
        ),
        'description' => array(
            'type' => 'text',
            'label' => ('分组描述'),
            'required' => false,
            'default' => '',
            'in_list' => true,
        ),
        'from_time' => array(
            'type' => 'time',
            'label' => ('交易开始时间'),
            'default' => 0,
            'in_list' => true,
            'required' => true,
        ),
        'to_time' => array(
            'type' => 'time',
            'label' => ('交易截止时间'),
            'default' => 0,
            'in_list' => true,
            'required' => true,
        ),
        'order_status' => array(
            'type' => array(
                '0' =>'全部交易客户',
                '1' =>'待付款',
                '2' =>'待发货',
                '3' =>'已发货',
                '4' =>'已作废',
                '5' =>'已完成',
            ),
            'label' => ('交易状态'),
            'default' => '0',
            'in_list' => true,
            'required' => true,
        ),

        'status' => array(
            'type' => array(
                '0' =>'统计中',
                '1' =>'统计完成'
            ),
            'default' => '0',
            'required' => true,
            'label' => ('状态'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'nums' => array(
            'type' => 'number',
            'label' => ('会员数量'),
            'default' => 0,
            'in_list' => true,
        ),
        'conditions' => array(
            'type' => 'serialize',
            'default' => '',
            'required' => true,
            'label' => ('规则条件'),

        )
    ),
    'comment' => ('用户分组'),
);
