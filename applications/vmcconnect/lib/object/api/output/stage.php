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

class vmcconnect_object_api_output_stage {

    protected $_tpl_output;

    public function __construct($app) {
        $this->app = $app;
    }

    function set_com_tpl($com_tpl) {
        $this->_tpl_output = !$com_tpl ? 'def' : $com_tpl;
        return $this;
    }
    
    function _tpl_output() {
        return $this->_tpl_output;
    }
    
    function _obj_output() {
        static $_obj;
        if ($_obj) return $_obj;
        $obj_cls = 'vmcconnect_object_api_output_' . $this->_tpl_output . '_global';
        $_obj = class_exists($obj_cls) ? vmc::singleton($obj_cls) : null;
        return $_obj;
    }
    
    public function get_out_datas(&$encode_datas, $method, $datas) {
        !$datas && $datas = array();
        $method = trim($method);
        if(!$method) return $datas;
        return $this->_obj_output()->output_datas($encode_datas, $method, $datas);
    }
    
    public function out_json($out_datas) {
        return $this->_obj_output()->out_json($out_datas);
    }

    public function out_xml($out_datas) {
        return $this->_obj_output()->out_xml($out_datas);
    }
}
