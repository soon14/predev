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
            'type_id' => 'type_id',
            'type_name' => 'name',
            'type_params' => 'params',
            'assrule' => 'assrule',
        ),
        'input' => array(
        ),
        'output' => array(
        ),
    ),
    // goodattrs.read.get - 获取商品类型列表 
    'read_get' => array(
        'fields' => array(
        ),
        'input' => array(
            'fields' => 'fields',
            'page' => 'page',
            'page_size' => 'pageSize',
        ),
        'output' => array(
        ),
    ),
    // goodattrs.read.valuesByAttrId - 获取商品类型属性 
    'read_valuesByAttrId' => array(
        'fields' => array(
            'type_id' => 'type_id',
            'type_name' => 'name',
            'type_params' => 'params',
            'assrule' => 'assrule',
            'props' => 'props',
        ),
        'input' => array(
            'type_id' => 'type_id',
            'fields' => 'fields',
        ),
        'output' => array(
        ),
    ),
);
