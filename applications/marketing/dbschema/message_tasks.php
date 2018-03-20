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

$db['message_tasks'] = array(
    'columns' => array(
        'task_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'ID',
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'name' => array(
            'type' => 'varchar(255)',
            'label' => ('营销名称'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'group_id' => array(
            'type' => 'serialize',
            'required' => true,
            'label' => ('会员分组')
        ),
        'tmpl_id' => array(
            'type' => 'table:message_tmpl',
            'required' => true,
            'label' => ('模板'),
            'in_list' => true,
        ),
        'title' => array(
            'type' => 'varchar(255)',
            'label' => ('标题'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'content' => array(
            'type' => 'text',
            'required' => true,
            'label' => ('内容'),
        ),

        'message_type' => array(
            'type' => array(
                'sms' =>'短信',
                'email' =>'邮件'
            ),
            'required' => true,
            'label' => ('类型'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'create_time' => array(
            'type' =>'time',
            'label' => ('创建时间'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'send_time' => array(
            'type' =>'time',
            'label' => ('发送时间'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'send_status' => array(
            'type' =>array(
                '0' =>'待执行',
                '1' =>'执行中',
                '2' =>'已完成'
            ),
            'default'=>'0',
            'label' => ('发送状态'),
            'in_list' => true,
            'default_in_list' => true,
        ),

    ),
    'comment' => ('消息'),
);