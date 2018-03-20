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
$db['customer_logistic'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ) ,
        'member_id' =>array(
            'type' => 'table:members@b2c',
            'required' => true,
            'default'=>0,
            'label' => ('会员用户名') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'corp_code' =>
            array (
                'type' => 'varchar(200)',
                'label' => ('物流公司代码'),
                'default_in_list' => false,
                'in_list' => true,
            ),
        'dly_corp' => array(
            'type' => 'varchar(200)',
            'default' => '',
            'label' => '物流公司' ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'logistic_no' => array(
            'type' => 'varchar(64)',
            'default' => '',
            'label' => '物流单号' ,
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
            'filtertype' => 'has',
        ) ,
        'logistic_log' => array(
            'type' => 'text',
            'default' => '',
            'comment' => '跟踪记录' ,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => '创建时间' , //创建时间
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',

        ) ,
        'dtline' => array(
            'type' => 'time',
            'label' => '最后同步时间' , //最后同步时间
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',

        ) ,
        'pulltimes' => array(
            'type' => 'number',
            'default' =>0,
            'label' => '查询次数' , // api查询次数
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'message' => array(
            'type' => 'varchar(255)',
            'label' => '监控状态相关消息' ,
            'in_list' => true,
        ) ,
        'status' =>array(
            'type' => array(
                'polling' =>'监控中',
                'shutdown' =>'结束',
                'abort' =>'终止',
                'updateall' =>'重新推送',
            ),
            'label' => '监控状态' ,
            'in_list' => true,
        ) ,
    ),
    'comment' => '客户物流状态记录表' ,
);