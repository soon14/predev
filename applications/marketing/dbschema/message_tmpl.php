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

$db['message_tmpl'] = array(
    'columns' => array(
        'tmpl_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'ID',
            'pkey' => true,
            'extra' => 'auto_increment',
            'in_list' => true,
        ),
        'name' => array(
            'type' => 'varchar(255)',
            'label' => ('模板名称'),
            'in_list' => true,
            'default_in_list' => true,
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

    ),
    'comment' => ('消息'),
);