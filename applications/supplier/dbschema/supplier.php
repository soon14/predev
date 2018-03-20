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

$db['supplier'] = array(
    'columns' => array(
        'supplier_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => '供应商信息id',
        ),
        'supplier_bn' => array(
            'type' => 'varchar(20)',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => '编号',
            'searchtype' => 'has',
        ),
        'supplier_name' => array(
            'type' => 'varchar(50)',
            'required' => true,
            'is_title'=>true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => '名称',
            'searchtype' => 'has',
        ),
        'supplier_manager' => array(
            'type' => 'varchar(50)',
            'in_list' => true,
            'default_in_list' => true,
            'label' => '联系人',
        ),
        'supplier_region' => array(
            'type' => 'region',
            'label' => '所在地区',
            'in_list' => true,
        ),
        'supplier_address' => array(
            'type' => 'varchar(200)',
            'label' => '联系地址',
        ),
        'supplier_link1' => array(
            'type' => 'varchar(50)',
            'comment' => '联系方式1',
            'in_list' => true,
            'default_in_list' => true,
            'label' => '联系方式1',
        ),
        'supplier_link2' => array(
            'type' => 'varchar(50)',
            'comment' => '联系方式2',
            'in_list' => true,
            'default_in_list' => true,
            'label' => '联系方式2',
        ),
        'supplier_description' => array(
            'type' => 'longtext',
            'comment' => '企业描述',
        ),
        'supplier_status' => array(
            'default' => '0',
            'type' => array(
                '0' => '有效',
                '1' => '终止',
            ),
            'label' => '合作状态',
            'required' => true,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
        ),
        'member_id' => array(
            'type' => 'table:members@pam',
            'required' => false,
            'comment' => '关联前台用户id',
        ),
        'dlyplace_send' => array(
            'type' => 'table:dlyplace@b2c',
            'required' => false,
            'label' => '发货地点',
            'in_list' => true,
        ),
        'dlyplace_reship' => array(
            'type' => 'table:dlyplace@b2c',
            'required' => false,
            'label' => '退货地点',
            'in_list' => true,
        ),
    ),
    'index' => array(
        'ind_supplier_bn' => array(
            'columns' => array(
                    0 => 'supplier_bn',
                ),
        ),
        'ind_member_id' => array(
            'columns' => array(
                    0 => 'member_id',
                ),
        ),
    ),
    'comment' => '供应商资料表' ,
);
