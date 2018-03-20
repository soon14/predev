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

class vmcconnect_object_test_api extends vmcconnect_object_test_global {

    protected $config = array(
        'app_key' => '1',
        'access_token' => 'EfO7MIqeuOOxtrsquHpcm8XemimxkAiz',
        'api_url' => 'https://beta.huaboxiangdada.com/openapi/vmcconnect/json',
            //'api_url' => 'http://develop.vmcshop.com/openapi/vmcconnect/json',
            //'api_url' => 'http://hb.vmc.com/openapi/vmcconnect/json',
    );
    protected $api_method;
    protected $api_params;

    public function __construct($app) {
        parent::__construct($app);
    }

    public function test() {
        $this->_test('vmc.category.read.getAll');
    }

    protected function _test($name) {
        $this->api_method = trim($name);
        $name = strtolower($this->api_method);
        if (strpos($name, $this->name_pre) === 0) {
            $name = substr($name, strlen($this->name_pre));
        }
        $func_name = str_replace('.', '_', $name);
        $func = '_test_' . $func_name;
        method_exists($this, $func) && call_user_func_array(array($this, $func), array());
    }

    protected function _sign_str($vars) {
        if (!$vars || !is_array($vars)) return false;
        ksort($vars);

        $arr = array();
        foreach ($vars as $_k => $_v) {
            $arr[] = $_k . (is_array($_v) ? json_encode($_v) : $_v);
        }
        $str = $this->config['access_token'] . implode('', $arr) . $this->config['access_token'];
        $sign = strtoupper(md5($str));
        return $sign;
    }

    protected function _params($params) {
        !$params && $params = array();
        ksort($params);
        $vars = array();
        $vars['app_key'] = $this->config['app_key'];
        $vars['timestamp'] = date('Y-m-d H:i:s');
        //$vars['timestamp'] = '2017-06-2110:38:36';
        $vars['method'] = $this->api_method;
        $vars['vmc_param_json'] = json_encode($params);
        ksort($vars);
        $_sign = $this->_sign_str($vars);
        $vars['sign'] = $_sign;
        //myfun::vard($vars);
        return $vars;
    }

    protected static function http_build_query($params) {
        $res = null;
        if ($params && is_array($params)) {
            $_tmp = array();
            foreach ($params as $key => $val) {
                $_tmp[] = $key . '=' . $val;
            }
            $res = implode('&', $_tmp);
        }
        return $res;
    }

    public function _send($params) {
        if (!$params || !is_array($params)) return false;

        $url = $this->config['api_url'];

        $post_data = $this->http_build_query($params);

        $http_client = vmc::singleton('base_httpclient');
        $res = $http_client->post($url, $post_data);
        $res && $res = json_decode($res);
        myfun::vard($res);
        return;
        // 使用系统post方法

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $return = curl_exec($ch);
        myfun::vard($return);
        curl_close($ch);

        return $return;
    }

    public function _test_category_write_add() {
        $vars = array();
        $this->api_params = $this->_params($vars);
        $this->_send($this->api_params);

        myfun::vard(__METHOD__, $params);
    }

    public function _test_system_ping() {
        $vars = array();
        $this->api_params = $this->_params($vars);
        $this->_send($this->api_params);

        myfun::vard(__METHOD__, $params);
    }

    public function _test_category_read_getAll() {
        $vars = array();
        $this->api_params = $this->_params($vars);
        $this->_send($this->api_params);

        myfun::vard(__METHOD__, $params);
    }

    public function _test_goods_sku_stock_write_update() {
        //myfun::vard(__METHOD__);
        $vars = array();
        $vars['quantity'] = 6000;
        $vars['sku_id'] = '13123';
        $this->api_params = $this->_params($vars);
        $this->_send($this->api_params);

        myfun::vard(__METHOD__, $params);
    }

    public function _test_distribution_read_get() {
        $vars = array();
        $this->api_params = $this->_params($vars);
        $this->_send($this->api_params);

        myfun::vard(__METHOD__, $params);
    }

    public function _test_delivery_read_get() {
        $vars = array();
        $this->api_params = $this->_params($vars);
        $this->_send($this->api_params);

        myfun::vard(__METHOD__, $params);
    }

    public function _test_pay_read_get() {
        $vars = array();
        $this->api_params = $this->_params($vars);
        $this->_send($this->api_params);

        myfun::vard(__METHOD__, $params);
    }

}
