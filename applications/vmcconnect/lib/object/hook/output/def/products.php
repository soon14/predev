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

class vmcconnect_object_hook_output_def_products extends vmcconnect_object_hook_output_def_base {
    
    public function __construct($app) {
        parent::__construct($app);
        $this->_name = 'products';
    }

    public function create() {
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;
        $data = ($params && isset($params['data'])) ? $params['data'] : array();
        $data = $this->get_params($data, $fields_map);
        return $data;
    }

    public function update() {
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;
        
        
        $products = $params && isset($params['data']) ? $params['data'] : null;
        $filter = $params && isset($params['filter']) ? $params['filter'] : null;
        $data_products = $products ? $this->get_params($products, ($fields_map && isset($fields_map['products']) ? $fields_map['products'] : null)) : null;
        $data_filter = $filter ? $this->get_params($filter, ($fields_map && isset($fields_map['filter']) ? $fields_map['filter'] : null)) : null;
        $res = array(
            'products' => $data_products,
            'filter' => $data_filter,
        );
        return $res;
    }

    public function delete() {
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $filter = $params && isset($params['filter']) ? $params['filter'] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;
        $data_filter = $filter ? $this->get_params($filter, $fields_map) : null;
        $res = array(
            'filter' => $data_filter,
        );
        return $res;
    }

}
