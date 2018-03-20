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

class vmcconnect_object_api_input_stage {

    protected $_tpl_input = 'def';
    protected $_obj_input = null;

    public function __construct($app) {
        $this->app = $app;
    }

    public function get_params($params = null) {
        $params = (!$params || !is_array($params)) ? vmc::singleton('base_component_request')->get_params(true) : $params;

        $app_key = null;
        $com_tpl = null;

        if ($params && (isset($params['app_key']) || isset($params['app_id']))) {
            $app_key = isset($params['app_key']) ? $params['app_key'] : (isset($params['app_id']) ? $params['app_id'] : null);
        }

        if ($params && (isset($params['com_tpl']) || isset($params['tpl']))) {
            $com_tpl = isset($params['com_tpl']) ? $params['com_tpl'] : (isset($params['tpl']) ? $params['tpl'] : null);
        }
        !$com_tpl && $app_key && $com_tpl = $this->_get_app_com_tpl($app_key);
        !$com_tpl && $com_tpl = 'def';

        $this->_tpl_input = $com_tpl;
        
        $params['app_key'] = $app_key;
        $params['com_tpl'] = $com_tpl;
        
        return $this->tpl_input_params($params);
    }

    protected function _get_app_com_tpl($app_key) {
        $app_key = (int) $app_key;
        if (!$app_key) return false;
        $_app = app::get('vmcconnect')->model('apps')->dump($app_key);
        if (!$_app || !$_app['app_com_tpl']) return false;
        return $_app['app_com_tpl'];
    }

    function _tpl_input() {
        return $this->_tpl_input;
    }

    function _obj_input() {
        static $_obj;
        if ($_obj) return $_obj;
        $obj_cls = 'vmcconnect_object_api_input_' . $this->_tpl_input . '_global';
        $_obj = class_exists($obj_cls) ? vmc::singleton($obj_cls) : null;
        return $_obj;
    }
    
    function tpl_input_params($params){
        return $this->_obj_input()->input_params($params);
    }

}
