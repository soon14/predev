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
        // region_id, local_name, package, p_region_id, region_path, ordernum
        'fields' => array(
            'area_id' => 'region_id',
            'area_name' => 'local_name',
            'area_package' => 'package',
            'area_pid' => 'p_region_id',
            'area_path' => 'region_path',
        ),
        'input' => array(
        ),
        'output' => array(
        ),
    ),
    // areas.read.province.get - 获取省级地址列表——新省级地址接口 
    'read_province_get' => array(
        'fields' => array(
        ),
        'input' => array(
            'fields' => 'fields',
        ),
        'output' => array(
        ),
    ),
    // areas.read.city.get - 获取市级信息列表——新市级地址接口 
    'read_city_get' => array(
        'fields' => array(
        ),
        'input' => array(
            'fields' => 'fields',
            'area_pid' => 'parent_id',
        ),
        'output' => array(
        ),
    ),
    // areas.read.county.get - 获取区县级信息列表——新区县级地址接口 
    'read_county_get' => array(
        'fields' => array(
        ),
        'input' => array(
            'fields' => 'fields',
            'area_pid' => 'parent_id',
        ),
        'output' => array(
        ),
    ),
    // areas.read.town.get - 获取乡镇级信息列表——新乡镇级地址接口 
    'read_town_get' => array(
        'fields' => array(
        ),
        'input' => array(
            'fields' => 'fields',
            'area_pid' => 'parent_id',
        ),
        'output' => array(
        ),
    ),
);
