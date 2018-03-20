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

class vmcconnect_object_api_input_def_goodattrs extends vmcconnect_object_api_input_def_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->pack_name = 'goodattrs';
    }

    // goodattrs.read.get - 获取商品类型列表 
    public function read_get() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // goodattrs.read.valuesByAttrId - 获取商品类型属性 
    public function read_valuesByAttrId() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

}
