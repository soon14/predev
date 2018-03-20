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


$db['media'] = array(
    'columns' => array(
        'replyrule_id' => array(
          'type' => 'table:replyrule',
          'required' => true,
          'default'=>0,
          'pkey' => true,
          'comment' => 'replyrule_id',
        ),
        'bind_id' => array(
            'type' => 'table:bind',
        ),
        'wmedia_id' => array(
            'type' => 'varchar(100)',
            'label' => '微信素材ID',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'wtype' => array(
            'type' => array(
                'news' => '图文',
                'image' => '纯图片',
                'video' => '视频',
                'voice' => '语音',
                'music' => '音乐',
                'text' => '简单文字',
            ),
            'label' => '微信素材类型',
            'default'=>'text',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'wtitle' => array(
            'type' => 'varchar(255)',
            'is_title' => 'true',
            'label' => '素材名称',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'wcontent' => array(
            'type' => 'serialize',
            'required' => true,
            'comment' => '素材内容',
        ),
        'update_time' => array(
            'type' => 'varchar(20)',
            'required' => true,
            'default' => '',
            'label' => '素材最后更新时间',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'sync_time' => array(
            'type' => 'last_modify',
        ),

    ),
    'comment' => '自动回复素材',
);
