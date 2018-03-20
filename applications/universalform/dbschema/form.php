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

$db['form'] = array(
    'columns' => array(
        'form_id' => array(
            'pkey' => true,
            'type' => 'number',
            'required' => true,
            'extra' => 'auto_increment',
            'label' => ('表单ID'),
            'comment' => '表单ID',
        ),
        'name' => array(
            'type' => 'varchar(255)',
            'required' => true,
            'label' => '表单名称',
            'comment' => '表单名称',
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'vmobile' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 'true',
            'label' => '是否需要验证手机',
            'comment' => '是否需要验证手机',
            'filtertype' => 'custom',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'vcode' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 'true',
            'label' => '是否需要验证码',
            'comment' => '是否需要验证码',
            'filtertype' => 'custom',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'times_submit' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 'true',
            'label' => '是否允许多次提交',
            'comment' => '是否允许多次提交',
            'filtertype' => 'custom',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => ('更新时间') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'orderby' => true,
        ) ,
    )
);