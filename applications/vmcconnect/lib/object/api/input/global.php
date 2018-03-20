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

class vmcconnect_object_api_input_global {

    protected $_tpl = 'def';
    protected $_method_pre = 'vmc.';
    protected $_method = null;
    protected $_call_method = null;
    protected $_obj_map = null;
    protected $_com_params;
    protected $_method_json;
    protected $_method_params;

    public function __construct($app) {
        $this->app = $app;
        $this->_obj_map = vmc::singleton('vmcconnect_object_api_map');
    }

    function com_params($params) {
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;
        
        if($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/api/' . $this->_tpl . '/_input_var.php')){
            $res = (include $_tmp_file);
            return $res;
        }
        $res = $this->_params($params, (is_file($_tmp_file = $this->app->app_dir . '/vars/api/' . $this->_tpl . '/_input_var.php') ? include $_tmp_file : null));
        return $res;
    }

    function input_params($params) {
        $this->_com_params = $this->com_params($params);
        $this->_method = ($this->_com_params && isset($this->_com_params['method'])) ? $this->_com_params['method'] : null;
        $this->_call_method = $this->_call_method($this->_method);
        $this->_com_params['method'] = $this->_call_method;
        $this->_method_params = $this->_method_params();
        $res = $this->_com_params;
        $res['method_params'] = $this->_method_params;
        return $res;
    }

    protected function _call_method($method) {
        if (!$method) return false;
        if (strpos($method, $this->_method_pre) === 0) {
            $method = substr($method, strlen($this->_method_pre));
        }
        $rel_method = $this->_obj_map->get_rel_method($method, $this->_tpl);
        return $rel_method;
    }

    protected function _params($params, $conf = array()) {
        !$params && $params = array();
        if (!$conf) return $params;
        $res = array();
        foreach ($conf as $_k => $_v) {
            $res[$_k] = ($params && isset($params[$_v])) ? $params[$_v] : null;
        }
        return $res;
    }

    protected function _method_params() {
        $this->_method_json = ($this->_com_params && isset($this->_com_params['method_params'])) ? $this->_com_params['method_params'] : null;

        $this->_method_params = array();
        $_method_params = $this->_method_json ? (!is_array($this->_method_json) ? json_decode($this->_method_json, true) : $this->_method_json) : null;
        
        $_tmp_arr = explode('.', $this->_call_method);
        $_pack = array_shift($_tmp_arr);
        $_method = $_tmp_arr ? implode('_', $_tmp_arr) : null;
        
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;
        
        if($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/api/' . $this->_tpl . '/' . $_pack . '.php')){
            $_params_map = (include $_tmp_file);
        }else{
            $_params_map = (is_file($_tmp_file = $this->app->app_dir . '/vars/api/' . $this->_tpl . '/' . $_pack . '.php')) ? (include $_tmp_file) : null;
        }

        // 默认 map
        $_def_map = ($_params_map && isset($_params_map['__'])) ? $_params_map['__'] : null;
        // 方法 map
        $_method_map = ($_params_map && isset($_params_map[$_method])) ? $_params_map[$_method] : null;

        // 可接收 input 
        $_input_params = ($_method_map && isset($_method_map['input']) && $_method_map['input']) ? $_method_map['input'] : (($_def_map && isset($_def_map['input']) && $_def_map['input']) ? $_def_map['input'] : null);
        // 整理输入 只允许有效数据
        if ($_input_params && $_method_params) {
            foreach ($_method_params as $_k => $_v) {
                $_input_params && isset($_input_params[$_k]) && $this->_method_params[$_input_params[$_k]] = $_v;
            }
        }

        // 可接收 fields
        $_fields_map = ($_method_map && isset($_method_map['fields']) && $_method_map['fields']) ? $_method_map['fields'] : (($_def_map && isset($_def_map['fields']) && $_def_map['fields']) ? $_def_map['fields'] : null);
        // 整理fields
        if (isset($this->_method_params['fields'])) {

            $_params_fields = is_string($this->_method_params['fields']) ? explode(',', trim($this->_method_params['fields'])) : $this->_method_params['fields'];

            is_string($_params_fields) && !strlen($_params_fields) && $_params_fields = '*';
            if ($_fields_map && $_params_fields != '*') {

                $_tmp = array();
                foreach ($_params_fields as $_v) {
                    $_v = trim($_v);
                    isset($_fields_map[$_v]) && $_tmp[] = $_fields_map[$_v];
                }

                $this->_method_params['fields'] = implode(', ', $_tmp);
            }
        }
        
        // 如果有特殊方法
        $_cls = "vmcconnect_object_api_input_{$this->_tpl}_" . $_pack;
        if (!$_pack || !$_method || !class_exists($_cls)) return $this->_method_params;

        $_obj = vmc::singleton($_cls);
        
        if (!$_pack || !$_method || !method_exists($_obj, $_method)) return $this->_method_params;

        $this->_method_params = call_user_func_array(array($_obj, $_method), array($this->_method_params));

        return $this->_method_params;
    }

}
