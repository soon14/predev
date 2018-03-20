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


$db['account'] = array(
    'columns' => array(
        'account_id' => array(
            'type' => 'number',
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => '账户序号ID' ,
        ) ,
        'account_type' => array(
            'type' => 'varchar(30)',
            'comment' => '账户类型(会员和管理员等)' ,
        ) ,
        'login_name' => array(
            'type' => 'varchar(100)',
            'is_title' => true,
            'required' => true,
            'comment' => '登录用户名' ,
        ) ,
        'login_password' => array(
            'type' => 'varchar(32)',
            'required' => true,
            'comment' => '登录密码' ,
        ) ,
        'disabled' => array(
            'type' => 'bool',
            'default' => 'false',
        ) ,
        'createtime' => array(
            'type' => 'time',
            'comment' => '创建时间' ,
        ) ,
    ) ,
    'index' => array(
        'account' => array(
            'columns' => array(
                'account_type',
                'login_name',
            ) ,
            'prefix' => 'UNIQUE',
        ) ,
    ) ,
    'engine' => 'innodb',
    'comment' => '用户权限账户表' ,
);
