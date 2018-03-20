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
return array(
    // 默认
    '__' => array(
        'fields' => array(
            'category_id' => 'cat_id',
            'category_pid' => 'parent_id',
            'category_path' => 'cat_path',
            'category_name' => 'cat_name',
            'category_order' => 'p_order',
            'visible' => 'visible',
            'create_time' => 'create_time',
        ),
        'input' => array(
        ),
        'output' => array(
        ),
    ),
    // category.write.add - 添加分类 
    'write_add' => array(
        'fields' => array(
            'category_id' => 'cat_id',
            'create_time' => 'create_time',
        ),
        'input' => array(
            'category_pid' => 'parent_id',
            'category_name' => 'cat_name',
            'category_order' => 'p_order',
        ),
        'output' => array(
        ),
    ),
    // category.write.delete - 删除分类 
    'write_delete' => array(
        'fields' => array(
            'category_id' => 'cat_id',
            'modified' => 'modified',
        ),
        'input' => array(
            'category_id' => 'cat_id',
        ),
        'output' => array(
        ),
    ),
    // category.write.update - 更新分类 
    'write_update' => array(
        'fields' => array(
            'category_id' => 'cat_id',
            'modified' => 'modified',
        ),
        'input' => array(
            'category_id' => 'cat_id',
            'category_pid' => 'parent_id',
            'category_name' => 'cat_name',
            'category_order' => 'p_order',
        ),
        'output' => array(
        ),
    ),
    // category.read.getAll - 获取所有类目信息 
    'read_getAll' => array(
        'fields' => array(
            'category_id' => 'cat_id',
            'category_pid' => 'parent_id',
            'category_path' => 'cat_path',
            'category_name' => 'cat_name',
            'category_order' => 'p_order',
            'visible' => 'visible',
            'create_time' => 'create_time',
        ),
        'input' => array(
            'fields' => 'fields',
            'page' => 'page',
            'page_size' => 'pageSize',
        ),
        'output' => array(
        ),
    ),
    // category.read.getFront - 获取前台展示的分类 
    'read_getFront' => array(
        'fields' => array(
            'category_id' => 'cat_id',
            'category_pid' => 'parent_id',
            'category_path' => 'cat_path',
            'category_name' => 'cat_name',
            'category_order' => 'p_order',
            'visible' => 'visible',
            'create_time' => 'create_time',
        ),
        'input' => array(
            'fields' => 'fields',
            'page' => 'page',
            'page_size' => 'pageSize',
        ),
        'output' => array(
        ),
    ),
    // category.read.findById - 获取单个类目信息 
    'read_findById' => array(
        'fields' => array(
        ),
        'input' => array(
            'category_id' => 'cat_id',
            'fields' => 'fields',
        ),
        'output' => array(
        ),
    ),
    // category.read.findByPId - 查找子类目列表 
    'read_findByPId' => array(
        'fields' => array(
        ),
        'input' => array(
            'category_pid' => 'parent_id',
            'fields' => 'fields',
            'page' => 'page',
            'page_size' => 'pageSize',
        ),
        'output' => array(
        ),
    ),
);
