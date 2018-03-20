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


$db['log'] = array(
    'columns' => array(
        'log_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ) ,
        'templ_type' => array(
            'type' => array(
                'pc' => ('PC 模板') ,
                'mobile' => ('H5 模板'),
                'widgets' =>'板块',
            ),
            'label' => ('类型') ,
            'default'=>'pc',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'templ_dir' => array(
            'type' => 'varchar(80)',
            'required' => true,
            'label' => '模板目录名',
            'in_list' => true,
            'default_in_list' => true,
            'comment' => '主题唯一目录英文名称',
            'order' => '3',
        ),
        'templ_path' => array(
            'type' => 'varchar(100)',
            'required' => true,
            'default' => '',
            'label' => ('路径') ,
            'in_list' => true,
        ),
        'templ_name' => array(
            'type' => 'varchar(200)',
            'required' => true,
            'default' => '',
            'label' => ('名称') ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'content' => array(
            'type' => 'text',
            'label' => ('内容') ,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => ('创建时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '7',
        ) ,
    ) ,
    'index' => array(
        'ind_templ_dir' => array(
            'columns' => array(
                0 => 'templ_dir',
            ) ,
        ),
        'ind_templ_path' => array(
            'columns' => array(
                0 => 'templ_path',
            ) ,
        ),
        'ind_createtime' => array(
            'columns' => array(
                0 => 'createtime',
            ),
        ),
    ) ,
    'engine' => 'innodb',
    'comment' => ('模板修改记录表') ,
);
