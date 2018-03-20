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

$db['store'] = array(
    'columns' => array(
        'store_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'sno' => array(
            'type' => 'varchar(50)',
            //'required' => true,
            'label' => '店铺编号',
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
        ),
        'name' => array(
            'type' => 'varchar(50)',
            'required' => true,
            'label' => ('店铺名称') ,
            'comment' => ('店铺名称') ,
            'is_title'=>true,
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
        ) ,
        'enterprise_id' => array(
            'type' => 'number',
            'label' => '所属企业',
            'comment' => '所属企业',
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
        ),
        'area' => array(
            'type' => 'region',
            'label' => '所属地区',
            'comment' => ('地区') ,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ) ,
        'addr' => array(
            'type' => 'varchar(50)',
            'label' => '店铺地址',
            'comment' => ('联系地址') ,
            'in_list' => true,
        ) ,
        'director_name' => array(
            'type' => 'varchar(50)',
            'required' => true,
            'label' => ('负责人姓名') ,
        ) ,
        'mobile' => array(
            'type' => 'varchar(50)',
            'required' => true,
            'label' => ('手机号码') ,
        ) ,
        'director_front' => array(
            'type' => 'char(32)',
            'comment' => ('负责人身份证正面') ,
        ) ,
        'director_reverse' => array(
            'type' => 'char(32)',
            'comment' => ('负责人身份证反面') ,
        ) ,
        'image_default_id' => array(
            'type' => 'varchar(32)',
            'label' => ('默认图片') ,
        ) ,
        'status' => array(
            'type' => array(
                '0' => '未审核',
                '1' =>  '审核通过',
                '2' =>  '审核未通过',
            ),
            'default' => '0',
            'label' => '审核状态',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ),
        'account_type' => array(
            'type' => array(
                '0' => '对公',
                '1' =>  '对私',
            ),
            'label' => '账户类型',
        ),
        'bank' => array(
            'type'=>'varchar(30)',
            'label' => '开户行支行名称',
        ),
        'account_name' => array(
            'type'=>'varchar(30)',
            'label' => '银行开户名称',
        ),
        'account' => array(
            'type'=>'varchar(255)',
            'label' => '银行账号',
        ),
        'line_number' => array(
            'type'=>'varchar(255)',
            'label' => '开户行支行联行号',
        ),
        'apply_time' => array(
            'type' => 'time',
            'label' => '注册时间',
            'comment' => '创建时间',
            'in_list'=> true,
            'default_in_list' => true,
            'orderby' => true,
            'filtertype' => 'yes',
        ),
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => '更新时间',
            'comment' => '创建时间',
        )


    )
);
