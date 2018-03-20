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


$db['center'] = array(
    'columns' => array(
        'center_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => ('中台ID') ,
        ) ,
        'center_name' => array(
            'type' => 'varchar(150)',
            'label' => ('中台名称') ,
            'is_title' => true,
            'required' => true,
            'comment' => ('中台名称') ,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'center_bn' => array(
            'type' => 'varchar(100)',
            'label' => ('中台编号') ,
            'required' => true,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
            'comment' => ('中台编号') ,
        ) ,
        'center_area' => array(
            'type' => 'region',
            'in_list' => true,
            'default_in_list' => true,
            'label' => ('所在地区') ,
            'comment' => ('所在地区') ,
        ) ,
        'center_address' => array(
            'type' => 'varchar(255)',
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
            'comment' => ('详细地址/门牌') ,
            'label' => ('详细地址/门牌') ,
        ) ,
        'center_contact' => array(
            'type' => 'varchar(100)',
            'comment' => ('联系方式') ,
            'label' => ('联系方式') ,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'store' => array(
            'type' => 'serialize',
            'required' => true,
            'comment' => ('下辖店铺') ,
            'label' => ('下辖店铺') ,
        ) ,
    ) ,
    'comment' => ('中央收银台表') ,
);