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

class vmcconnect_object_api_output_global {

    protected $_tpl = 'def';
    protected $_method_pre = 'vmc.';
    protected $_method = null;
    protected $_call_method = null;
    protected $_ref_method = null;
    protected $_obj_map = null;
    protected $_method_map;

    public function __construct($app) {
        $this->app = $app;
        $this->_obj_map = vmc::singleton('vmcconnect_object_api_map');
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

    protected function _err_codes() {
        static $_error_codes;
        if ($_error_codes) return $_error_codes;
        $_error_codes = array();

        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;

        if ($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/api/' . $this->_tpl . '/error_code.php')) {
            $_error_codes = (include $_tmp_file);
            return $_error_codes;
        } elseif (is_file($_tmpFile = $this->app->app_dir . '/vars/api/' . $this->_tpl . '/error_code.php')) {
            $_error_codes = include $_tmpFile;
            return $_error_codes;
        }
        
        is_file($_tmpFile = $this->app->app_dir . '/vars/api/error_code.php') && $_error_codes = include $_tmpFile;
        return $_error_codes;
    }

    protected function _err_ref($code) {
        static $_error_maps;
        
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;

        if (!$_error_maps && $ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/api/' . $this->_tpl . '/error_maps.php')) {
            $_error_maps = (include $_tmp_file);
        }
        
        !$_error_maps && is_file($_tmpFile = $this->app->app_dir . '/vars/api/' . $this->_tpl . '/error_maps.php') && $_error_maps = include $_tmpFile;
        if ($_error_maps && isset($_error_maps[$code]))
                return $_error_maps[$code];
        return $code;
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

        if ($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/api/' . $this->_tpl . '/_output_var.php')) {
            $_var_map = (include $_tmp_file);
        }
        
        !$_var_map && $_var_map = (is_file($_tmp_file = $this->app->app_dir . '/vars/api/' . $this->_tpl . '/_output_var.php') ? include $_tmp_file : null);

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

        if ($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/api/' . $this->_tpl . '/' . $_pack . '.php')) {
            $this->_method_map = (include $_tmp_file);
        }else{
            $this->_method_map = (is_file($_tmp_file = $this->app->app_dir . '/vars/api/' . $this->_tpl . '/' . $_pack . '.php')) ? (include $_tmp_file) : null;
        }
        
        return $this->_method_map;
    }

    protected function error($datas) {
        $error = (isset($datas['code'])) ? $datas['code'] : 0;
        $err_msg = (isset($datas['msg'])) ? $datas['msg'] : null;
        $err_msg_strs = (isset($datas['msg_strs'])) ? $datas['msg_strs'] : null;

        $_err_codes = $this->_err_codes();
        $_rel_error = $this->_err_ref($error);
        $_err_info = $_err_codes && isset($_err_codes[$_rel_error]) ? $_err_codes[$_rel_error] : null;
        $_error_msg = $_err_info['msg_cn'];
        if ($err_msg_strs) {
            $_fields_map = $this->_method_field_map();
            if ($_fields_map) {
                foreach ($err_msg_strs as $_k => $_v) {
                    $_search = array_search($_v, $_fields_map);
                    !$_search && $_search = $_v;
                    $err_msg_strs[$_k] = $_search;
                }
            }
            $call_array = array($_error_msg);
            $call_array = array_merge($call_array, $err_msg_strs);
            $_error_msg = call_user_func_array('sprintf', $call_array);
        }

        // 
        $data = array();
        $data['code'] = $_rel_error;
        $data['msg'] = $_error_msg;
        return $data;
    }

    protected function _datas($params, $conf = array()) {
        !$params && $params = array();
        if (!$conf) return $params;
        $res = array();
        foreach ($conf as $_k => $_v) {
            $res[$_k] = ($params && isset($params[$_v])) ? $params[$_v] : null;
        }
        return $res;
    }

    function out_datas($datas) {
        $_output_vars = $this->_method_var_map();
        $out_data = array();
        if ($_output_vars) {
            foreach ($_output_vars as $k => $_v) {
                $out_data[$k] = ($datas && isset($datas[$_v])) ? $datas[$_v] : null;
            }
        } else {
            $out_data = $datas;
        }
        return $out_data;
    }

    function output_datas(&$encode_datas, $method, $datas) {
        $this->_call_method($method);
        $this->_ref_method($this->_call_method);

        $datas = $this->out_datas($datas);

        if (isset($datas['code']) && $datas['code']) {
            $encode_datas = $this->error($datas);
            return $encode_datas;
        }
        unset($datas['msg_strs']);

        $_tmp_arr = explode('.', $this->_call_method);
        $_pack = array_shift($_tmp_arr);
        $_method = $_tmp_arr ? implode('_', $_tmp_arr) : null;
        $_field_map = $this->_method_field_map();

        // 整理fields
        if ($datas && $datas['result'] && $_field_map) {

            $_result = array();
            foreach ($datas['result'] as $_k => $_v) {
                if (is_numeric($_k) && is_array($_v)) {
                    foreach ($_v as $_ck => $_cv) {
                        if ($_search = array_search($_ck, $_field_map)) {
                            $_result[$_k][$_search] = $_cv;
                        }
                    }
                } else {
                    if ($_search = array_search($_k, $_field_map)) {
                        $_result[$_search] = $_v;
                    }
                }
            }
            $datas['result'] = $_result;
        }

        $_call_class = "vmcconnect_object_api_output_" . $this->_tpl . "_" . $_pack;
        if (!class_exists($_call_class)) {
            $encode_datas = $datas;
            return $datas;
        }

        $_obj = vmc::singleton($_call_class);
        if (!method_exists($_obj, $_method)) {
            $encode_datas = $datas;
            return $datas;
        }
        $datas = call_user_func_array(array($_obj, $_method), array($datas));
        $encode_datas = $datas;
        return $datas;
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

    /*
     * 数组转xml
     * 
     */

    private function _array2xml($data, $item = 'item', $id = 'id') {
        $xml = $attr = '';
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key = $item;
            }
            $flag = true;
            if (is_array($val) && (array_keys($val) === range(0, count($val) - 1))) {
                $flag = false;
            }
            $xml .= $flag ? "<{$key}{$attr}>" : "";
            $xml .= (is_array($val) || is_object($val)) ? $this->_array2xml($val, is_string($key) ? $key : $item, $id) : $val;
            $xml .= $flag ? "</{$key}>" : "";
        }
        return $xml;
    }

    /*
     * 输出XML
     */

    public function out_xml($data) {
        header("Content-type: text/xml");

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<Response method="' . $this->_method_pre . $this->_ref_method . '.response">';
        $xml .= $this->_array2xml($data, '', '');
        $xml .= "</Response>";

        echo $xml;
    }

}
