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

$db['report'] = array(
    'columns' => array(
        'report_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'ID',
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'task_id' => array(
            'type' => 'table:message_tasks',
            'required' => true,
            'label' => '活动名称',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'name' => array(
            'type' => 'varchar(255)',
            'required' => true,
            'label' => ('营销名称'),
            'in_list' => true,
            'default_in_list' => true,
        ),

        'send_time' => array(
            'type' =>'time',
            'label' => ('发送时间'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'send_nums' => array(
            'type' =>'number',
            'label' => ('成功发送人数'),
            'default'=>0,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'order_total' => array(
            'type' =>'money',
            'label' => ('销售金额'),
            'default'=>0,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'order_count' => array(
            'type' =>'number',
            'label' => ('订单数量'),
            'default'=>0,
            'in_list' => true,
            'default_in_list' => true,
        ),

    ),
    'comment' => ('营销报告'),
);