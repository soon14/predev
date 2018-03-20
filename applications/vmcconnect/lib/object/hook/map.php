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

class vmcconnect_object_hook_map {
    protected $app;
    
    public function __construct($app) {
        $this->app = $app;
    }

    public function setApp(&$app) {
        $this->app = $app;
    }
    
    protected function _get_hooks() {
        
        $_get_apis = false;
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;
        
        if($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/hook/hooks')){
            $_get_apis = (include $_tmp_file);
        }else{
            $_get_apis = file($this->app->app_dir . '/vars/hook/hooks');
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

    public function all_hooks() {
        static $all_hooks;
        if ($all_hooks) return $all_hooks;
        
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;
        
        if($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/hook/items.php')){
            $all_hooks = (include $_tmp_file);
            return $all_hooks;
        }
        
        $all_hooks = include $this->app->app_dir . '/vars/hook/items.php';
        return $all_hooks;
    }

    public function all_hook_items() {
        static $all_hook_items;
        if ($all_hook_items) return $all_hook_items;

        $all_hooks = $this->all_hooks();
        $all_hook_items = array();
        if (!$all_hooks) return false;
        foreach ($all_hooks as $_k => $_v) {
            if (!isset($_v['items']) || !$_v['items']) continue;
            foreach ($_v['items'] as $_tk => $_tv) {
                $all_hook_items[$_tk] = $_tv;
            }
        }
        
        return $all_hook_items;
    }
    
    public function ref_maps($type){
        $type = strtolower(trim($type));
        !$type && $type = 'def';
        static $maps;
        if($maps && isset($maps[$type])) return $maps[$type];
        
        $map = false;
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;
        
        if($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/hook/_' . $type . '_map.php')){
            $map = (include $_tmp_file);
        }else{
            $map = is_file($map_file = $this->app->app_dir . '/vars/hook/_' . $type . '_map.php') ? (include $map_file) : null;
        }
        
        $map && $maps[$type] = $map;
        return $map;
    }
    
    public function get_rel_method($method, $tpl) {
        $method = strtolower(trim($method));
        $tpl = trim($tpl);
        $map = $this->ref_maps($tpl);
        if(!$map || !isset($map[$method])) return $method;
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
        if($sys_conf_allow_items) return $sys_conf_allow_items;
        
        $get_conf = $this->app->getConf('vmcconnect-hook-conf');
        $hook_conf = $get_conf ? unserialize($get_conf) : null;
        $sys_conf_allow_items = ($hook_conf && $hook_conf['hook_items']) ? $hook_conf['hook_items'] : null;
        if(!$sys_conf_allow_items) return false;
        return $sys_conf_allow_items;
    }
    
    public function sys_allow_hooks(){
        static $sys_allow_hooks;
        if($sys_allow_hooks) return $sys_allow_hooks;
        
        $sys_allow_hooks = $this->all_hooks();
        if(!$sys_allow_hooks) return false;
        
        $allow_items = $this->sys_conf_allow_items();
        if(!$allow_items) return false;
        
        foreach ($sys_allow_hooks as $_k => $_v){
            if($_v['items']){
                foreach ($_v['items'] as $_ck => $_cv){
                    if(!in_array($_ck, $allow_items)){
                        unset($sys_allow_hooks[$_k]['items'][$_ck]);
                    }
                }
            }
            if(!$sys_allow_hooks[$_k]['items']) unset($sys_allow_hooks[$_k]);
        }
        return $sys_allow_hooks;
    }
    
    public function sys_allow_hook_items(){
        static $sys_allow_hook_items;
        if($sys_allow_hook_items) return $sys_allow_hook_items;
        
        $sys_allow_hooks = $this->sys_allow_hooks();
        if(!$sys_allow_hooks) return false;
        
        $sys_allow_hook_items = array();
        foreach ($sys_allow_hooks as $_k => $_v){
            if($_v['items']){
                $sys_allow_hook_items = array_merge($sys_allow_hook_items, array_keys($_v['items']));
            }
        }
        return $sys_allow_hook_items;
    }
    
    public function app_allow_hooks($app_id){
        $app_id = (int)$app_id;
        if(!$app_id) return false;
        
        $mod_app_items = app::get('vmcconnect')->model('app_items');
        $allow_items = $mod_app_items->get_allow_hook_items($app_id);
        
        if(!$allow_items) return false;
        
        $sys_allow_hooks = $this->sys_allow_hooks();
        if(!$sys_allow_hooks) return false;
        
        foreach ($sys_allow_hooks as $_k => $_v){
            if($_v['items']){
                foreach ($_v['items'] as $_ck => $_cv){
                    if(!in_array($_ck, $allow_items)){
                        unset($sys_allow_hooks[$_k]['items'][$_ck]);
                    }
                }
            }
            if(!$sys_allow_hooks[$_k]['items']) unset($sys_allow_hooks[$_k]);
        }
        return $sys_allow_hooks;
    }
    

}
