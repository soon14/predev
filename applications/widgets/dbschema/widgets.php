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


$db['widgets'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ) ,
        'tmp_name' => array(
            'type' => 'varchar(200)',
            'required' => true,
            'default' => '',
            'label' => ('板块模板名称') ,
            'is_title' => true,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
            'order' => '1',
        ) ,
        'type' => array(
            'type' => array(
                '0' => ('图文展示') ,
                '1' => ('商品展示') ,
                '2' => ('自定义代码') ,
                '3' => ('静态板块') ,
            ),
            'label' => ('板块类型') ,
            'searchtype' => 'has',
            'default'=>'0',
            'in_list' => true,
            'default_in_list' => true,
            'order' => '2',
        ) ,
        'code' => array(
            'type' => 'varchar(100)',
            'required' => true,
            'default' => 0,
            'label' => ('英文名称') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
            'order' => '3',
        ),
        'weight' => array(
            'type' => 'number',
            'required' => true,
            'default' => 0,
            'label' => ('权重') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
            'order' => '3',
        ),
        'cid' => array(
            'type' => 'table:widgets_category',
            'default' =>0,
            'label' => ('板块分类名称') ,
            'in_list' => true,
            'default_in_list' => true,
            'order' => '4',
        ),
        'screen'=>array(
            'type' => array(
                '0' => ('pc') ,
                '1' => ('移动') ,
            ),
            'default'=>'0',
            'label'=>'适用屏幕',
            'order' => '4',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => ('更新时间') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
            'order' => '5',
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => ('创建时间') ,
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
            'order' => '6',
        ) ,
        'file_path' =>array(
            'type' => 'varchar(255)',
            'default' => '',
            'label' => ('文件相对路径') ,
            'comment' => ('文件相对路径') ,
            'in_list' => true,
            'default_in_list' => true,
        ),
    ) ,
    'index' => array(
        'ind_name' => array(
            'columns' => array(
                0 => 'tmp_name',
            ) ,
        ),
        'ind_code' => array(
            'columns' => array(
                0 => 'code',
            ) ,
        ) ,
        'ind_createtime' => array(
            'columns' => array(
                0 => 'createtime',
            ),
        ),
        'ind_last' => array(
            'columns' => array(
                0 => 'last_modify',
            ),
        ),
    ) ,
    'engine' => 'innodb',
    'comment' => ('板块表') ,
);
