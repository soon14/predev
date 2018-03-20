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



$db['normallogs']=array (
    'columns' =>
    array (
        'id' =>
        array (
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'username' =>
        array (
            'type' => 'varchar(50)',
            'required' => true,
            'label' => '操作员',
            'searchtype' => 'has',
            'filtertype' => 'yes',
            
            'width' => 70,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'module' =>
        array (
            'type' => 'varchar(50)',
            'required' => true,
            'label' => '模块',

        ),
        'operate_type' =>
        array (
            'type' => 'varchar(255)',
            'required' => true,
            'label' => '动作',
            'width' => 200,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'searchtype' => 'has',
        ),
        'dateline' =>
        array (
            'type' => 'time',
            'required' => true,
            'label' => '操作时间',
            'filtertype' => 'time',
            

            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        ),
        'memo' =>
        array (
            'type' => 'longtext',
            'label' => '日志内容',

            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'searchtype' => 'has',
        ),
    ),
    'index' =>
    array (
        'ind_dateline' =>
        array (
          'columns' =>
          array (
            0 => 'dateline',
          ),
        ),
    ),
);
