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

class vmcconnect_object_api_map {

    protected $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function setApp(&$app) {
        $this->app = $app;
    }

    protected function _get_apis() {
        
        $_get_apis = false;
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;
        
        if($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/api/apis')){
            $_get_apis = (include $_tmp_file);
        }else{
            $_get_apis = file($this->app->app_dir . '/vars/api/apis');
        }
        
        $apis = array();
        $curr_key = null;
        foreach ($_get_apis as $v) {
            $v = trim($v);
            if (!strlen($v)) continue;
            if ($v[0] == '-') {
                $_tmp = explode('-', ltrim($v, '-'));
                $curr_key = trim($_tmp[0]);
                $_name = trim($_tmp[1]);
                $apis[$curr_key] = array(
                    'name' => $_name,
                    'items' => array(),
                );
            } elseif (strpos($v, '=')) {
                if (!$curr_key) continue;
                $_tmp = explode('=', $v);
                $_key = trim($_tmp[0]);
                $_name = trim($_tmp[1]);
                $apis[$curr_key]['items'][$_key] = $_name;
            }
        }
        $apis_str = var_export($apis, true);
        exit;
    }

    public function all_apis() {
        static $all_apis;
        if ($all_apis) return $all_apis;
        
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;
        
        if($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/api/items.php')){
            $all_apis = (include $_tmp_file);
            return $all_apis;
        }
        
        $all_apis = include $this->app->app_dir . '/vars/api/items.php';
        return $all_apis;
    }

    public function all_api_items() {
        static $all_api_items;
        if ($all_api_items) return $all_api_items;
        $all_apis = $this->all_apis();
        $all_api_items = array();
        if (!$all_apis) return false;
        foreach ($all_apis as $_k => $_v) {
            if (!isset($_v['items']) || !$_v['items']) continue;
            foreach ($_v['items'] as $_tk => $_tv) {
                $all_api_items[$_tk] = $_tv;
            }
        }

        return $all_api_items;
    }

    public function ref_maps($type = null) {
        $type = strtolower(trim($type));
        !$type && $type = 'def';
        static $maps;
        if ($maps && isset($maps[$type])) return $maps[$type];
        
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;
        
        if($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/api/_' . $type . '_map.php')){
            $map = (include $_tmp_file);
        }else{
            $map = is_file($map_file = $this->app->app_dir . '/vars/api/_' . $type . '_map.php') ? (include $map_file) : null;
        }
        
        $map && $maps[$type] = $map;
        return $map;
    }

    public function get_rel_method($method, $tpl) {
        $method = trim($method);
        $tpl = trim($tpl);
        $map = $this->ref_maps($tpl);
        if (!$map || !isset($map[$method])) return $method;
        return $map[$method];
    }
    
    public function get_ref_method($method, $tpl) {
        $method = trim($method);
        $tpl = trim($tpl);
        $map = $this->ref_maps($tpl);
        $search = $map ? array_search($method, $map) : $method;
        !$search && $search = $method;
        return $search;
    }

    public function sys_conf_allow_items() {
        static $sys_conf_allow_items;
        if ($sys_conf_allow_items) return $sys_conf_allow_items;

        $get_conf = $this->app->getConf('vmcconnect-api-conf');
        $api_conf = $get_conf ? unserialize($get_conf) : null;
        $sys_conf_allow_items = ($api_conf && $api_conf['api_items']) ? $api_conf['api_items'] : null;
        if (!$sys_conf_allow_items) return false;
        return $sys_conf_allow_items;
    }

    public function sys_allow_apis() {
        static $sys_allow_apis;
        if ($sys_allow_apis) return $sys_allow_apis;

        $sys_allow_apis = $this->all_apis();
        if (!$sys_allow_apis) return false;

        $allow_items = $this->sys_conf_allow_items();
        if (!$allow_items) return false;

        foreach ($sys_allow_apis as $_k => $_v) {
            if ($_v['items']) {
                foreach ($_v['items'] as $_ck => $_cv) {
                    if (!in_array($_ck, $allow_items)) {
                        unset($sys_allow_apis[$_k]['items'][$_ck]);
                    }
                }
            }
            if (!$sys_allow_apis[$_k]['items']) unset($sys_allow_apis[$_k]);
        }
        return $sys_allow_apis;
    }

    public function sys_allow_api_items() {
        static $sys_allow_api_items;
        if ($sys_allow_api_items) return $sys_allow_api_items;

        $sys_allow_apis = $this->sys_allow_apis();
        if (!$sys_allow_apis) return false;

        $sys_allow_api_items = array();
        foreach ($sys_allow_apis as $_k => $_v) {
            if ($_v['items']) {
                $sys_allow_api_items = array_merge($sys_allow_api_items, array_keys($_v['items']));
            }
        }
        return $sys_allow_api_items;
    }

}
