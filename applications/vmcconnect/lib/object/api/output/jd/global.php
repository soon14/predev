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

class vmcconnect_object_api_output_jd_global extends vmcconnect_object_api_output_global {

    public function __construct($app) {
        parent::__construct($app);
        $this->_tpl = 'jd';
    }
    
    public function out_json($data) {
        header('Content-Type:application/json; charset=utf-8');
        $_response_key = $this->_method_pre . ($this->_ref_method ? $this->_ref_method : 'error') . '.response';
        $_key = implode('_', explode('.', $_response_key));
        
        $_out_data = array();
        $_out_data[$_key] = $data;

        echo json_encode($_out_data);
        unset($_out_data);
    }

}
