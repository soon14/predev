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


$db['bind'] = array(
    'columns' => array(
        'id' => array(
          'type' => 'int unsigned',
          'required' => true,
          'pkey' => true,
          'extra' => 'auto_increment',
          'comment' => 'ID',
        ),
        'name' => array(
            'type' => 'varchar(100)',
            'required' => true,
            'default' => '',
            'is_title' => 'true',
            'label' => '公众账号名称',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'eid' => array(
            'type' => 'varchar(100)',
            'required' => true,
            'comment' => '微信公众账号api中标识',
        ),
        'wechat_id' => array(
            'type' => 'varchar(100)',
            'required' => true,
            'default' => '',
            'comment' => '原始ID',
        ),
        'wechat_account' => array(
            'type' => 'varchar(20)',
            'required' => true,
            'default' => '',
            'label' => '微信号',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'status' => array(
            'type' => array(
                'active' => ('启用'),
                'disabled' => ('禁用'),
            ),
            'default' => 'active',
            'required' => true,
            'label' => ('状态'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'wechat_type' => array(
            'type' => array(
                'subscription' => '订阅号',
                'service' => '服务号',
            ),
            'required' => true,
            'default' => 'subscription',
            'label' => '微信账号类型',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'email' => array(
            'type' => 'varchar(30)',
            'required' => true,
            'label' => '登录邮箱',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'avatar' => array(
            'type' => 'char(32)',
            'label' => '头像',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'url' => array(
            'type' => 'varchar(100)',
            'required' => true,
            'comment' => '接口配置URL',
        ),
        'token' => array(
            'type' => 'varchar(100)',
            'required' => true,
            'comment' => '接口配置token',
        ),
        'appid' => array(
            'type' => 'varchar(100)',
            'comment' => 'AppId',
        ),
        'appsecret' => array(
            'type' => 'varchar(100)',
            'comment' => 'AppSecret',
        ),
        'aeskey' => array(
            'type' => 'text',
            'comment' => 'aeskey',
        ),
        'qr' => array(
            'type' => 'char(32)',
            'label' => '二维码',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'menu'=>array(
            'type'=>'serialize',
            'label'=>'菜单序列化数组'
        )

    ),
    'index' => array(
        'eid' => array('columns' => array('eid'),'prefix' => 'UNIQUE'),
    ),
    'comment' => '微信公众账号绑定列表',
);
