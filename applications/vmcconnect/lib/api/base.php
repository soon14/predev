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

class vmcconnect_api_base extends base_openapi {
    
    //
    protected $api_status = null;

    // 应用的APP_KEY
    protected $_app_key = null;
    // 应用的app_info
    protected $_app_info = null;
    // 应用的APP_SECRET
    protected $_app_secret = null;
    // 所支持的版本
    protected $_act_versions = array(
        '1.0',
    );
    // 当前使用的版本
    protected $_act_version = '1.0';
    // 所支持的命令模版
    protected $_com_tpls = array(
        'def',
        'jd',
    );
    // 当前使用的命令模版
    protected $_com_tpl = '';
    // 所有传入参数
    protected $_params = null;
    // 方法前缀
    protected $_pre_method = 'vmc.';
    // 传入的方法
    protected $_method = null;
    // 所传入的方法参数
    protected $_method_params = null;
    // 所支持的输出方式
    protected $_out_types = array(
        'json',
        'xml',
    );
    // 当前输出方法
    protected $_out_type = null;
    // 当前输出数据
    protected $_out_data = null;
    // exception
    protected $_exception = null;
    // global 方法类
    protected $_obj_global = null;

    /*
     * 构造方法
     * @param object $app 
     */

    public function __construct(&$app) {
        $this->app = $app;
        $this->get_api_status();
        $this->_obj_global = vmc::singleton('vmcconnect_object_api_global');
        $this->_obj_global->setapp($this->app);
        $this->_obj_global->setRefObj($this);
    }

    // set || get
    // app_key
    public function set_app_key($var) {
        $this->_app_key = $var;
    }

    public function get_app_key() {
        return $this->_app_key;
    }
    
    // app_info
    public function set_app_info($var) {
        $this->_app_info = $var;
    }

    public function get_app_info() {
        return $this->_app_info;
    }

    // app_secret
    public function set_app_secret($var) {
        $this->_app_secret = $var;
    }

    public function get_app_secret() {
        return $this->_app_secret;
    }

    // act_versions
    public function set_act_versions($var) {
        $this->_act_versions = $var;
    }

    public function get_act_versions() {
        return $this->_act_versions;
    }

    // act_version
    public function set_act_version($var) {
        $this->_act_version = $var;
    }

    public function get_act_version() {
        return $this->_act_version;
    }

    // com_tpls
    public function set_com_tpls($var) {
        $this->_com_tpls = $var;
    }

    public function get_com_tpls() {
        return $this->_com_tpls;
    }

    // com_tpl
    public function set_com_tpl($var) {
        $this->_com_tpl = $var;
    }

    public function get_com_tpl() {
        return $this->_com_tpl;
    }

    // app_key
    public function set_params($var) {
        $this->_params = $var;
    }

    public function get_params() {
        return $this->_params;
    }

    // pre_method
    public function set_pre_method($var) {
        $this->_pre_method = $var;
    }

    public function get_pre_method() {
        return $this->_pre_method;
    }

    // app_key
    public function set_method($var) {
        $this->_method = $var;
    }

    public function get_method() {
        return $this->_method;
    }

    // method_params
    public function set_method_params($var) {
        $this->_method_params = $var;
    }

    public function get_method_params() {
        return $this->_method_params;
    }

    // out_types
    public function set_out_types($var) {
        $this->_out_types = $var;
    }

    public function get_out_types() {
        return $this->_out_types;
    }

    // out_type
    public function set_out_type($var) {
        $this->_out_type = $var;
    }

    public function get_out_type() {
        return $this->_out_type;
    }

    // out_data
    public function set_out_data($var) {
        $this->_out_data = $var;
    }

    public function get_out_data() {
        return $this->_out_data;
    }

    // obj_global
    public function set_obj_global($var) {
        $this->_obj_global = $var;
    }

    public function get_obj_global() {
        return $this->_obj_global;
    }
    
    //
    function get_api_status(){
        if (!is_null($this->api_status)) return $this->api_status;
        $get_conf = $this->app->getConf('vmcconnect-api-conf');
        $_conf = $get_conf ? unserialize($get_conf) : null;
        $this->api_status = ($_conf && isset($_conf['api_enable']) && $_conf['api_enable']) ? true : false;
        unset($get_conf, $_conf);
        return $this->api_status;
    }

    /*
     * 初始化参数
     */

    protected function init() {
        try {
            $this->_obj_global->get_request_params();
            $this->_obj_global->check_sys_params();
            return $this;
        } catch (Exception $exc) {
            $this->_exception_handler($exc);
            return $this;
        }
    }

    /*
     * 运行
     */

    public function run() {
        if ($this->_exception)
            return $this;
        try {
            $this->_obj_global->call_method();
            return $this;
        } catch (Exception $exc) {
            $this->_exception_handler($exc);
            return $this;
        }
    }

    /*
     * 输出
     */

    protected function out() {
        !strlen($this->_out_type) && $this->_out_type = 'json';
        method_exists($this->_obj_global, ($_method = 'out_' . $this->_out_type)) && call_user_func_array(array($this->_obj_global, $_method), array());
        return $this;
    }

    /*
     * 异常显示
     */

    public function _exception_handler($exception) {
        $_code = $exception->getCode();
        $_msg = $exception->getMessage();
        !$_code && $_code = 1;
        $this->_out_data = array();
        $this->_out_data['code'] = $_code;
        $this->_exception = $exception;
        return $this->out();
        
    }

}
