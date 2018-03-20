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


$db['xcxtplmsg'] = array(
    'columns' => array(
        'id' => array(
          'type' => 'int unsigned',
          'required' => true,
          'pkey' => true,
          'extra' => 'auto_increment',
          'comment' => 'ID',
        ),
        'msg_type'=>array(
            'type'=>array(
                "AT0002"=>"下单成功通知",
                "AT0007"=>"订单发货提醒"
            ),
            'in_list' => true,
            'default_in_list' => true,
            'label'=>'消息通知模板'
        ),
        'touser' => array(
            'type' => 'varchar(100)',
            'label' => '用户OpenID',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'form_id' => array(
            'type' => 'varchar(100)',
            'label' => '小程序FORM_ID',

            'in_list' => true,
            'default_in_list' => true,
        ),
        'order_id' => array(
            'type' => 'table:orders@b2c',
            'label' => '订单号',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'template_id' => array(
            'type' => 'varchar(100)',
            'label' => '消息模板ID',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'page' => array(
            'type' => 'varchar(255)',
            'label' => '详情小程序地址',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'data' => array(
            'type' => 'serialize',
            'label' => '模板数据',
        ),
        'emphasis_keyword' => array(
            'type' => 'varchar(50)',
            'label' => '高亮数据项',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'send_log' => array(
            'type' => 'serialize',
            'label' => '消息发送日志',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'send_status' => array(
            'type'=>array(
                "normal"=>"未发送",
                "succ"=>"发送成功",
                "error"=>"发送异常"
            ),
            'required'=>true,
            'default'=>'normal',
            'label' => '消息发送状态',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'timestamp' => array(
            'type' => 'time',
            'label' => ('发送时间'),
            'order' => true,
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
    ),
    'comment' => '小程序模板消息',
);
