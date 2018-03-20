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

class vmcconnect_object_api_input_def_areas extends vmcconnect_object_api_input_def_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->pack_name = 'areas';
    }

    // areas.read.province.get - 获取省级地址列表——新省级地址接口 
    public function read_province_get() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // areas.read.city.get - 获取市级信息列表——新市级地址接口 
    public function read_city_get() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // areas.read.county.get - 获取区县级信息列表——新区县级地址接口 
    public function read_county_get() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // areas.read.town.get - 获取乡镇级信息列表——新乡镇级地址接口 
    public function read_town_get() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

}
