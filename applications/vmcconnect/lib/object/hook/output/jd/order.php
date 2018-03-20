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

class vmcconnect_object_hook_output_jd_order extends vmcconnect_object_hook_output_jd_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->_name = 'order';
    }

    public function create(){
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;
        $data = ($params && isset($params['data'])) ? $params['data'] : array();
        return $this->get_params($data, $fields_map);
    }
    
    public function cancel(){
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;
        $data = ($params && isset($params['data'])) ? $params['data'] : array();
        return $this->get_params($data, $fields_map);
    }
    
    public function end(){
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;
        $data = ($params && isset($params['data'])) ? $params['data'] : array();
        return $this->get_params($data, $fields_map);
    }
}
