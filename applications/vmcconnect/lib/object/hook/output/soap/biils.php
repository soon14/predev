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

class vmcconnect_object_hook_output_soap_biils extends vmcconnect_object_hook_output_soap_base {
    
    public function __construct($app) {
        parent::__construct($app);
        $this->_name = 'biils';
    }

    public function payment_succ(){
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;
        
        $data = ($params && isset($params['data'])) ? $params['data'] : array();
        return $this->get_params($data, $fields_map);
    }
    
    public function payment_progress(){
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $data = ($params && isset($params['data'])) ? $params['data'] : array();
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;
        return $this->get_params($data, $fields_map);
    }
    
    public function refund_succ(){
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $data = ($params && isset($params['data'])) ? $params['data'] : array();
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;
        return $this->get_params($data, $fields_map);
    }
    
    public function refund_progress(){
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $data = ($params && isset($params['data'])) ? $params['data'] : array();
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;
        return $this->get_params($data, $fields_map);
    }
}
