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

class vmcconnect_object_hook_output_soap_base {

    protected $_name = null, $_obj_map = null, $_tpl = 'def';

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
    
    public function _send($client, $datas, $hook, $params) {
        if (!$client || !$hook) return false;

        $data = $datas['method_params'];
        $data = $data ? (!is_array($data) ? json_decode($data, true) : $data) : false;
        $data && $data = array($data);
        
        $arr = explode('.', $datas['method']);
        array_shift($arr);
        $method = $arr ? implode('_', $arr) : false;
        if(!$method) return false;

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        try {

            $res = $client->$method($json);
            if(!$res) return false;
            $datas['res'] = $res;
            return $datas;
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

}
