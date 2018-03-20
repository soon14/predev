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

$db['enterprise'] = array(
    'columns' => array(
        'enterprise_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'eno' => array(
            'type' => 'varchar(50)',
            'required' => true,
            'label' => '企业编号',
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
        ),
        'name' => array(
            'type' => 'varchar(50)',
            'required' => true,
            'label' => ('企业名称') ,
            'in_list' => true,
            'is_title'=>true,
            'default_in_list' => true,
            'searchtype' => 'has',
        ) ,
        'area' => array(
            'type' => 'region',
            'label' => ('所属地区') ,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ) ,
        'addr' => array(
            'type' => 'varchar(50)',
            'comment' => ('地址') ,
        ) ,
        'director_name' => array(
            'type' => 'varchar(50)',
            'required' => true,
            'label' => ('负责人姓名') ,
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
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
        'logo' => array(
            'type' => 'char(32)',
            'comment' => ('图片/logo') ,
        ) ,
        'business_license' => array(
            'type' => 'varchar(255)',
            'comment' => '营业执照注册号',
        ),
        'business_image' => array(
            'type' => 'char(32)',
            'comment' => '营业执照照片',
        ),
        'organization_code' => array(
            'type'=>'varchar(255)',
            'label' => '组织机构代码',
        ),
        'organization_image' => array(
            'type' => 'char(32)',
            'comment' => '营业执照照片',
        ),
        'legal_person_name' => array(
            'type'=>'varchar(50)',
            'label' => '法人姓名',
        ),
        'legal_person_id' => array(
            'type'=>'varchar(18)',
            'label' => '法人身份证号码',
        ),
        'legal_front' => array(
            'type' => 'char(32)',
            'comment' => ('法人身份证正面') ,
        ) ,
        'legal_reverse' => array(
            'type' => 'char(32)',
            'comment' => ('法人身份证反面') ,
        ) ,
        'status' => array(
            'type' => array(
                '0' => '未审核',
                '1' =>  '审核通过',
                '2' =>  '审核未通过',
            ),
            'default' => '0',
            'label' => '审核状态',
            'default_in_list' => true,
            'in_list' => true,
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
            'comment' => '更新时间',
            'filtertype' => 'yes',
        )


    )
);
