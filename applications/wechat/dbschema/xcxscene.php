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


$db['xcxscene'] = array(
    'columns' => array(
        'scene_key' => array(
          'type' => 'char(32)',
          'required' => true,
          'pkey' => true,
          'comment' => 'scene_key',
        ),
        'scene_path' => array(
            'type' => 'varchar(255)',
            'label' => '小程序页面路径',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'createtime' => array(
            'type' => 'time',
            'required' => true,
            'label' => '创建时间',
            'in_list' => true,
            'default_in_list' => true,
        ),
    ),
    'comment' => '小程序场景二维码map',
);
