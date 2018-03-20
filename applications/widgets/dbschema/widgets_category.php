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
$db['widgets_category'] = array(
    'columns' =>array(
        'cid' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'title' =>array(
            'type' => 'varchar(100)',
            'required' => true,
            'default' => '',
            'label' => ('分类名称') ,
            'comment' => ('分类中文名称') ,
            'is_title' => true,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'category_key' =>array(
            'type' => 'varchar(100)',
            'required' => true,
            'default' => '',
            'label' => ('英文标识') ,
            'comment' => ('分类英文名称') ,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'parent_id' =>array(
            'type' => 'number',
            'required' => true,
            'default' => 0,
            'label' => ('上级分类') ,
            'comment' => ('上级分类id') ,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'parent_path' =>array(
            'type' => 'varchar(100)',
            'required' => true,
            'default' => '',
            'label' => ('分类节点') ,
            'comment' => ('分类节点') ,
        ),
        'has_children' =>array(
            'type' =>array(
                'false' =>'否',
                'true' =>'是' ,
            ),
            'default' =>'false',
            'required' => true,
            'label' => ('是否有子分类') ,
            'comment' => ('是否有子分类') ,
        ),
        'dir' =>array(
            'type' => 'varchar(255)',
            'required' => true,
            'default' => '',
            'label' => ('分类目录') ,
            'comment' => ('分类目录') ,
        )
    )
);