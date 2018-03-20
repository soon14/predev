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
$db['form_module'] = array(
    'columns' => array(
        'module_id' => array(
            'pkey' => true,
            'type' => 'bigint',
            'required' => true,
            'extra' => 'auto_increment',
            'label' => ('ID'),
            'comment' => 'ID',
        ),
        'form_id' => array(
            'type' => 'table:form',
            'required' => true,
            'label' => '表单ID',
            'comment' => '表单ID',
        ),
        'module_name' => array(
            'type' => 'varchar(255)',
            'required' => true,
            'label' => '名称',
            'comment' => '名称',
        ),
        'name' => array(
            'type' => 'varchar(255)',
            'required' => true,
            'label' => '字段name ',
            'comment' => '字段name ',
        ),
        'type' => array(
            'type' => array(
                'text' => '文本',
                'select' => '单选项',
                'checkbox' => '多选项',
                'date' => '日期选择器',
                'region' => '地区选择器',
                'image' => '单图上传器',
                'images' => '多图上传器',
            ),
            'default' => 'text',
            'required' => true,
            'label' => '字段类型',
            'comment' => '字段类型',
        ),
        'required' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 'false',
            'label' => '是否必须',
            'comment' => '是否必须',
        ),
        'options' => array(
            'type' => 'serialize',
            'label' => '多选类型',
            'comment' => '多选类型',
        ),
        'valtype' => array(
            'type' => 'varchar(255)',
            'label' => '限定值的类型',
            'comment' => '限定值的类型',
        ),
        'show' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 'true',
            'label' => '是否显示',
            'comment' => '是否显示',
        ),
        'm_order' => array(
            'type' => 'number',
            'default' => 0,
            'required' => true,
            'label' => ('权重') ,
            'comment' => ('排序') ,
            'filtertype' => 'normal',
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
        ) ,
    )
);