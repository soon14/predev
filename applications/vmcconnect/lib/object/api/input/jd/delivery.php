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

class vmcconnect_object_api_input_jd_delivery extends vmcconnect_object_api_input_jd_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->pack_name = 'delivery';
    }

    // delivery.write.add - 添加物流公司 
    public function write_add() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // delivery.write.edit - 添加物流公司 
    public function write_edit() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // delivery.write.delete - 删除物流公司 
    public function write_delete() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // delivery.read.get - 获取物流公司 
    public function read_get() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

}
