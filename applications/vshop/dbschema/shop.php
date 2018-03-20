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


$db['shop'] = array(
    'columns' => array(
        'shop_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'default' => 0,
            'pkey' => true,
            'label' => ('店铺ID'),
            'comment' => '店铺ID',
            'is_title' => true,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'name' => array(
            'type' => 'varchar(200)',
            'label' => '店铺名称',
            'comment' => '店铺名称',
            'required' => true,
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'logo' => array(
            'type' => 'varchar(32)',
            'label' => '店铺LOGO',
        ),
        'member_id' => array(
            'type' => 'table:members@b2c',
            'required' => true,
            'label' => '关联会员',
            'comment' => '关联会员',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'shop_lv_id' => array(
            'required' => true,
            'default' => 0,
            'label' => ('店铺等级'),
            'comment' => '店铺等级',
            'type' => 'table:lv',
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'shopkeeper' => array(
            'type' => 'varchar(200)',
            'label' => '店主姓名',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => false,
        ),
        'open_shopkeeper' => array(
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'in_list' => true,
            'filtertype' => 'normal',
            'default_in_list' => false,
            'label' => ('是否公开店主姓名'),
            'comment' => ('是否公开店主姓名'),
        ),
        // 'auth_code' => array(
        //     'type' => 'varchar(200)',
        //     'label' => '授权码',
        //     'comment' => '授权码',
        //     'required' => true,
        //     'searchtype' => 'normal',
        //     'filtertype' => 'normal',
        //     'in_list' => true,
        //     'default_in_list' => false,
        // ),
        'region' => array(
            'type' => 'region',
            'label' => '所属地区',
            'comment' => '所属地区',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'address' => array(
            'type' => 'varchar(255)',
            'label' => '实体店地址',
            'comment' => '实体店地址',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => false,
        ),
        'open_address' => array(
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'in_list' => true,
            'filtertype' => 'normal',
            'default_in_list' => false,
            'label' => ('是否公开地址'),
            'comment' => ('是否公开地址'),
        ),
        'contacts_phone' => array(
            'type' => 'varchar(50)',
            'label' => '联系方式',

            'comment' => '联系方式',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => false,
        ),
        'open_phone' => array(
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'in_list' => true,
            'filtertype' => 'normal',
            'default_in_list' => false,
            'label' => ('是否公开联系方式'),
            'comment' => ('是否公开联系方式'),
        ),
        'qq' => array(
            'type' => 'varchar(20)',
            'label' => 'QQ',
            'comment' => 'QQ',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => false,
        ),
        'open_qq' => array(
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'in_list' => true,
            'filtertype' => 'normal',
            'default_in_list' => false,
            'label' => ('是否公开QQ账号'),
            'comment' => ('是否公开QQ账号'),
        ),
        'wechat' => array(
            'type' => 'varchar(30)',
            'label' => '微信号',
            'comment' => '微信号',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => false,
        ),
        'open_wechat' => array(
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'in_list' => true,
            'filtertype' => 'normal',
            'default_in_list' => false,
            'label' => ('是否公开微信号'),
            'comment' => ('是否公开微信号'),
        ),
        'idcard' => array(
            'type' => 'varchar(18)',
            'default' => '0',
            'required' => true,
            'in_list' => true,
            'default_in_list' => false,
            'label' => ('身份证号码'),
            'comment' => ('身份证号码'),
        ),
        'idcard_front_image_id' => array(
            'type' => 'varchar(32)',
            'label' => '身份证(正面)图片',
            'comment' => '身份证(正面)图片',
        ),
        'idcard_back_image_id' => array(
            'type' => 'varchar(32)',
            'label' => '身份证(背面)图片',
            'comment' => '身份证(背面)图片',
        ),
        'gallery_image_id' => array(
            'type' => 'varchar(32)',
            'label' => '店招图',
            'comment' => '店招图',
        ),
        'visit_count' => array(
            'type' => 'int unsigned',
            'default' => 0,
            'required' => true,
            'filtertype' => 'normal',
            'label' => ('累计访问量'),
        ),
        'createtime' => array(
            'type' => 'time',
            'label' => '创建时间',
            'comment' => '创建时间',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        ),
        'lastmodify' => array(
            'type' => 'last_modify',
            'label' => '最后更新时间',
            'comment' => '最后更新时间',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        ),
        'status' => array(
            'type' => array(
                'validate' => ('验证中') ,
                'active' => ('正常合作中') ,
                'pause' => ('暂停营业') ,
            ) ,
            'filtertype' => 'normal',
            'default' => 'validate',
            'required' => true,
            'label' => '店铺状态',
            'in_list' => true,
            'default_in_list' => true
        ) ,
    ),
    //索引
    'index' => array(
        'ind_shop_id' => array(
            'columns' => array(
                0 => 'shop_id',
            ),
        ),
        'ind_createtime' => array(
            'columns' => array(
                0 => 'createtime',
            ),
        ),
        'ind_lastmodify' => array(
            'columns' => array(
                0 => 'lastmodify',
            ),
        ),
    ),
    'comment' => ('微店铺表'),
);
