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

class vmcconnect_object_hook_output_jd_base {

    protected $_name = null, $_obj_map = null, $_tpl = 'jd';

    public function __construct($app) {
        $this->app = $app;
        $this->_obj_map = vmc::singleton('vmcconnect_object_hook_map');
    }

    public function get_params($param, $fields_map = null) {
        !$param && $param = array();
        if (!$param || !$fields_map) return $param;

        $_res = array();
        foreach ($fields_map as $_k => $_v) {
            isset($param[$_v]) && $_res[$_k] = $param[$_v];
        }
        return $_res;
    }

    public function _method_name($method) {
        return ltrim(strrchr($method, ':'), ':');
    }

}
