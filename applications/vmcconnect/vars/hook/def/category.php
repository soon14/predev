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
        ),
        'output' => array(
        ),
    ),
    // category.save - 添加分类 
    'save' => array(
        'fields' => array(
        ),
        'output' => array(
        ),
    ),
    // category.remove - 移除分类 
    'remove' => array(
        'fields' => array(
            'category_id' => 'catid',
        ),
        'output' => array(
        ),
    ),
);
