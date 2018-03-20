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

class vmcconnect_object_hook_soap {

    public $app;

    public function __construct($app) {
        $this->app = app::get('vmcconnect');
    }

    public function send($datas, $hook, $params) {
        if (!$hook) return false;

        $method = trim($datas['method']);

        $_tmp_arr = explode('.', $method);
        array_shift($_tmp_arr);
        $pack = array_shift($_tmp_arr);
        $method = $_tmp_arr ? implode('_', $_tmp_arr) : null;

        $cls = 'vmcconnect_object_hook_output_soap_' . $pack;
        $obj = class_exists($cls) ? vmc::singleton($cls) : null;
        $call_method = 'send_' . $method;
        if (!$obj || (!method_exists($obj, $call_method) && !method_exists($obj, '_send'))) {
            return false;
        }

        $hook['hook_addon'] = $hook['hook_addon'] ? json_decode($hook['hook_addon'], true) : null;

        $client = $this->client($hook);
        if (!$client) return false;

        if (method_exists($obj, $call_method)) {
            return call_user_func_array(array($obj, $call_method), array($client, $datas, $hook, $params));
        } elseif (method_exists($obj, '_send')) {
            return call_user_func_array(array($obj, '_send'), array($client, $datas, $hook, $params));
        }
        return false;
    }

    public function client($hook) {
        $url = $hook && isset($hook['hook_url']) ? trim($hook['hook_url']) : false;
        if (!strlen($url)) return false;

        $addon = isset($hook['hook_addon']) ? $hook['hook_addon'] : false;
        $addon && $addon = !is_array($addon) ? json_decode($addon, true) : $addon;

        $options = $addon && $addon['options'] ? $addon['options'] : null;
        $header = $addon && $addon['header'] ? $addon['header'] : null;
        $namespace = $options && $options['uri'] ? $options['uri'] : null;

        try {
            $client = new SoapClient($url, $options);

            if ($header) {
                $soap_header = new SoapHeader($namespace, $header['name'], $header['data']);
                $client->__setSoapHeaders($soap_header);
            }
            return $client;
        } catch (Exception $exc) {
            return false;
        }
        return false;
    }

}
