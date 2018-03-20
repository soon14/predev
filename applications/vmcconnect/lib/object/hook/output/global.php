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

class vmcconnect_object_hook_output_global {

    protected $_tpl = 'def';
    protected $_method_pre = 'vmc.';
    protected $_method = null;
    protected $_call_method = null;
    protected $_ref_method = null;
    protected $_obj_map = null;
    protected $_method_map;

    public function __construct($app) {
        $this->app = $app;
        $this->_obj_map = vmc::singleton('vmcconnect_object_hook_map');
    }

    protected function _call_method($method) {
        if (!$method) return false;
        if (strpos($method, $this->_method_pre) === 0) {
            $method = substr($method, strlen($this->_method_pre));
        }
        $this->_call_method = $method;
        return $this->_call_method;
    }

    protected function _ref_method($method) {
        if (!$method) return false;
        if (strpos($method, $this->_method_pre) === 0) {
            $method = substr($this->_ref_method, strlen($this->_method_pre));
        }
        $this->_ref_method = $this->_obj_map->get_ref_method($method, $this->_tpl);
        return $this->_ref_method;
    }

    protected function _method_var_map() {
        static $_var_map;
        if ($_var_map) return $_var_map;

        $this->_method_fields_map();
        if (!$this->_method_map) return false;
        
        $_maps = $this->_method_map;

        $_tmp_arr = explode('.', $this->_call_method);
        $_pack = array_shift($_tmp_arr);
        $_method = $_tmp_arr ? implode('_', $_tmp_arr) : null;

        // 默认 map
        $_def_map = ($_maps && isset($_maps['__'])) ? $_maps['__'] : null;
        // 方法 map
        $_method_map = ($_maps && isset($_maps[$_method])) ? $_maps[$_method] : null;

        $_var_map = ($_method_map && isset($_method_map['output']) && $_method_map['output']) ? $_method_map['output'] : (($_def_map && isset($_def_map['output']) && $_def_map['output']) ? $_def_map['output'] : null);
        
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;
        if($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/hook/' . $this->_tpl . '/_output_var.php')){
            $_var_map = (include $_tmp_file);
            return $_var_map;
        }
        
        !$_var_map && $_var_map = (is_file($_tmp_file = $this->app->app_dir . '/vars/hook/' . $this->_tpl . '/_output_var.php') ? include $_tmp_file : null);

        return $_var_map;
    }

    protected function _method_field_map() {
        $this->_method_fields_map();
        if (!$this->_method_map) return false;
        $_maps = $this->_method_map;

        $_tmp_arr = explode('.', $this->_call_method);
        $_pack = array_shift($_tmp_arr);
        $_method = $_tmp_arr ? implode('_', $_tmp_arr) : null;

        // 默认 map
        $_def_map = ($_maps && isset($_maps['__'])) ? $_maps['__'] : null;
        // 方法 map
        $_method_map = ($_maps && isset($_maps[$_method])) ? $_maps[$_method] : null;

        $_fields_map = ($_method_map && isset($_method_map['fields']) && $_method_map['fields']) ? $_method_map['fields'] : (($_def_map && isset($_def_map['fields']) && $_def_map['fields']) ? $_def_map['fields'] : null);

        return $_fields_map;
    }

    protected function _method_fields_map() {
        if ($this->_method_map) return $this->_method_map;
        $_tmp_arr = explode('.', $this->_call_method);
        $_pack = array_shift($_tmp_arr);
        $_method = $_tmp_arr ? implode('_', $_tmp_arr) : null;
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;
        if($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/hook/' . $this->_tpl . '/' . $_pack . '.php')){
            $this->_method_map = (include $_tmp_file);
            return $this->_method_map;
        }
        $this->_method_map = (is_file($_tmp_file = $this->app->app_dir . '/vars/hook/' . $this->_tpl . '/' . $_pack . '.php')) ? (include $_tmp_file) : null;
        return $this->_method_map;
    }

    protected function datas($datas) {
        $_output_vars = $this->_method_var_map();
        $out_data = array();
        if ($_output_vars) {
            foreach ($_output_vars as $k => $_v) {
                ($datas && isset($datas[$_v])) && $out_data[$k] = $datas[$_v];
            }
        } else {
            $out_data = $datas;
        }
        return $out_data;
    }

    public function out_datas($datas, $method) {
        $this->_call_method($method);
        $this->_ref_method($this->_call_method);
        $datas = $this->datas($datas);
        
        !$datas && $datas = array();

        $data_json = ($datas && isset($datas['method_params'])) ? $datas['method_params'] : null;
        
        $_tmp_arr = explode('.', $this->_call_method);
        $_pack = array_shift($_tmp_arr);
        $_method = $_tmp_arr ? implode('_', $_tmp_arr) : null;
        $_field_map = $this->_method_field_map();
        
        $_call_class = "vmcconnect_object_hook_output_" . $this->_tpl . "_" . $_pack;
        if($data_json && class_exists($_call_class) && method_exists($_call_class, $_method)){
            $data_json = call_user_func_array(array(vmc::singleton($_call_class), $_method), array($data_json, $_field_map));
        }
        
        $data_json && ksort($data_json);

        $res = array();
        $res['app_key'] = ($datas && isset($datas['app_key'])) ? $datas['app_key'] : null;
        $res['hook_key'] = ($datas && isset($datas['hook_key'])) ? $datas['hook_key'] : null;
        $res['timestamp'] = ($datas && isset($datas['timestamp'])) ? $datas['timestamp'] : null;
        $res['method'] = $this->_method_pre . $this->_ref_method;
        $res['method_params'] = json_encode($data_json);
        
        return $res;
    }

    public function add_sign(&$datas, $sign) {
        $datas['sign'] = $sign;
        return $datas;
    }

}
