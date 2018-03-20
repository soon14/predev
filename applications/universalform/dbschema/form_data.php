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
$db['form_data'] = array(
    'columns' => array(
        'data_id' => array(
            'pkey' => true,
            'type' => 'bigint',
            'required' => true,
            'extra' => 'auto_increment',
            'label' => ('ID'),
            'comment' => 'ID',
        ),
        'form_id' => array(
            'type' => 'table:form',
            'required' => true,
            'label' => '表单ID',
            'comment' => '表单ID',
        ),
        'member_id' => array(
            'type' => 'table:members@b2c',
            'required' => true,
            'label' => '会员账号',
            'comment' => '会员ID',
            'default' => 0,
        ),
        'data' => array(
            'type' => 'serialize',
            'label' => '表单数据',
            'comment' => '表单数据',
        ),
        'createtime' => array(
            'type' => 'time',
            'label' => ('提交时间') ,
            'filtertype' => 'time',
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        ) ,
    )
);