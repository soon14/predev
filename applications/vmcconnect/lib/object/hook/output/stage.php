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

class vmcconnect_object_hook_output_stage {

    protected $_tpl_output = 'def', $_obj_map;

    public function __construct($app) {
        $this->app = $app;
        $this->_obj_map = vmc::singleton('vmcconnect_object_hook_map');
    }

    function set_msg_tpl($msg_tpl) {
        $this->_tpl_output = !$msg_tpl ? 'def' : $msg_tpl;
        return $this;
    }

    public function out_encode($datas, $hook) {
        return $this->_obj_output()->out_datas($datas, $hook);
    }

    function _tpl_output() {
        return $this->_tpl_output;
    }

    function _obj_output() {
        static $_obj;
        if ($_obj) return $_obj;
        $obj_cls = 'vmcconnect_object_hook_output_' . $this->_tpl_output . '_global';
        $_obj = class_exists($obj_cls) ? vmc::singleton($obj_cls) : null;
        return $_obj;
    }

    public function addSign(&$data, $sign) {
        return $this->_obj_output()->add_sign($data, $sign);
    }

    public function send($datas, $hook, $params) {
        $obj_cls = 'vmcconnect_object_hook_' . $this->_tpl_output;
        $_obj = class_exists($obj_cls) ? vmc::singleton($obj_cls) : null;
        if(!$_obj) return false;
        
        $res_data = false;
        
        if(method_exists($_obj, 'send')){
            $res_data = call_user_func_array(array($_obj, 'send'), array($datas, $hook, $params));
        }

        if (!$res_data) {
            return array(
                'error' => true,
            );
        }

        return array(
            'data' => $res_data,
        );
    }

}
