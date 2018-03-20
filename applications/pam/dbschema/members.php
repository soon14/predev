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


$db['members'] = array(
    'columns' => array(
        'member_id' => array(
            'type' => 'number',
            'pkey' => true,
            'comment' => '会员ID',
        ),
        'openid' => array(
            'type' => 'varchar(255)',
            'label' => 'openid',
        ),
        'unionid' => array(
            'type' => 'varchar(255)',
            'label' => 'unionid',
        ),
        'login_account' => array(
            'type' => 'varchar(100)',
            'is_title' => true,
            'required' => true,
            'comment' => '登录账号',
        ),
        'login_type' => array(
            'pkey' => true,
            'type' => array(
                'local' => '用户名',
                'mobile' => '手机号码',
                'email' => '邮箱',
                'wechat' => '微信',
                'qq' => 'QQ',
                'weibo' => '新浪微博',
                'taobao' => '淘宝',
                'baidu' => '百度',
                'alipay' => '支付宝',
                '163' => '网易',
                'renren' => '人人',
                'sohu' => '搜狐',
                'douban' => '豆瓣',
                'kaixin' => '开心网',
                '360' => '360账号',
                'toauth01' => '合作账户01',
                'toauth02' => '合作账户02',
                'toauth03' => '合作账户03',
                'toauth04' => '合作账户04',
                'toauth04' => '合作账户05',
            ),
            'default' => 'local',
            'comment' => '登录账号类型',
        ),
        'login_password' => array(
            'type' => 'varchar(32)',
            'required' => true,
            'comment' => '登录密码',
        ),
        'password_account' => array(
            'type' => 'varchar(100)',
            'required' => true,
            'comment' => '登录密码加密所用账号',
        ),
        'disabled' => array(
            'type' => 'bool',
            'default' => 'false',
        ),
        'createtime' => array(
            'type' => 'time',
            'comment' => '创建时间',
        ),
    ),
    'index' => array(
        'account' => array(
            'columns' => array(
                'login_account',
                'login_type',
            ) ,
            'prefix' => 'UNIQUE',
        ),
    ) ,
    'engine' => 'innodb',
    'comment' => '会员账号表',
);
