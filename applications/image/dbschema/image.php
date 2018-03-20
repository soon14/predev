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


$db['image'] = array(
    'columns' => array(
        'image_id' => array(
            'type' => 'char(32)',
            'label' => '图片ID' ,
            'required' => true,
            'pkey' => true,
        ) ,
        'storage' => array(
            'label' => '存储引擎' ,
            'type' => 'varchar(50)',
            'default' => 'filesystem',
            'required' => true,
            'in_list' => true,
            'searchtype'=>'has',
            'default_in_list' => false,
        ) ,
        'image_name' => array(
            'label' => '图片名称' ,
            'type' => 'varchar(255)',
            'required' => false,
            'in_list'=>true,
            'searchtype'=>'has',
            'default_in_list' => true,
        ) ,
        'ident' => array(
            'type' => 'varchar(200)',
            'required' => true,
        ) ,
        'url' => array(
            'label' => '原始图URL地址' ,
            'type' => 'varchar(255)',
            'required' => true,
            'searchtype'=>'has',
            'in_list' => false,
        ) ,
        'l_ident' => array(
            'type' => 'varchar(255)',
            'comment' => '大图唯一标识' ,
        ) ,
        'l_url' => array(
            'type' => 'varchar(255)',
            'comment' => '大图URL地址' ,
        ) ,
        'm_ident' => array(
            'type' => 'varchar(255)',
            'comment' => '中图唯一标识' ,
        ) ,
        'm_url' => array(
            'type' => 'varchar(255)',
            'comment' => '中图URL地址' ,
        ) ,
        's_ident' => array(
            'type' => 'varchar(255)',
            'comment' => '小图唯一标识' ,
        ) ,
        's_url' => array(
            'type' => 'varchar(255)',
            'comment' => '小图URL地址' ,
        ) ,
        'xs_ident' => array(
            'type' => 'varchar(255)',
            'comment' => '迷你图唯一标识' ,
        ) ,
        'xs_url' => array(
            'type' => 'varchar(255)',
            'comment' => '迷你图URL地址' ,
        ) ,
        'width' => array(
            'label' => '宽度' ,
            'type' => 'number',
        ) ,
        'height' => array(
            'label' => '高度' ,
            'type' => 'number',
        ) ,
        'watermark' => array(
            'type' => 'bool',
            'default' => 'false',
            'label' => '有水印' ,
        ) ,
        'last_modified' => array(
            'label' => '更新时间' ,
            'type' => 'last_modify',
            'required' => true,
            'default' => 0,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes'
        ) ,
    ) ,
    'engine' => 'innodb',
    'comment' => '图片表' ,
);
