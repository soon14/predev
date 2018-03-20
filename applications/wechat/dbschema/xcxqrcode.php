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


$db['xcxqrcode'] = array(
    'columns' => array(
        'qrcode_target_md5' => array(
          'type' => 'char(32)',
          'required' => true,
          'pkey' => true,
          'comment' => 'qrcode_md5',
        ),
        'image_id' => array(
            'type' => 'table:image@image',
        ),
        'qrcode_path' => array(
            'type' => 'varchar(255)',
            'label' => '小程序页面路径',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'qrcode_type' => array(
            'type' => array(
                'normal' => '普通码',
                'xcx' => '小程序风格',
            ),
            'label' => '二维码类型',
            'default' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'qrcode_width' => array(
            'type' => 'number',
            'required' => true,
            'default' => 430,
            'label' => '二维码宽度(px)',
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
    'comment' => '小程序二维码',
);
