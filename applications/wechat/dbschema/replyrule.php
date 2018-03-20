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

$db['replyrule'] = array(
    'columns' => array(
        'replyrule_id' => array(
          'type' => 'int unsigned',
          'pkey' => true,
          'extra' => 'auto_increment',
          'comment' => 'ID',
        ),
        'bind_id' => array(
          'type' => 'table:bind',
          'required' => true,
          'default' => 0,
          'filtertype' => 'normal',
          'label' => '微信公众号',
          'in_list' => true,
          'default_in_list' => true,
        ),
        'reply_type' => array(
            'type' => array(
                'subscribe' => '关注自动回复',
                'unsubscribe' => '取消关注自动回复',
                'keywords' => '关键词自动回复',
            ),
            'required' => true,
            'filtertype' => 'normal',
            'default' => 'keywords',
            'label' => '自动回复类型',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'keywords' => array(
            'type' => 'text',
            'label' => '关键词',
            'searchtype' => 'has',
            'filtertype' => 'yes',
            'filterdefault' => 'true',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'last_modify'=>array(
            'type'=>'last_modify'
        )
    ),
    'comment' => '自动回复规则',
);
