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

class vmcconnect_object_hook_output_def_delivery extends vmcconnect_object_hook_output_def_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->_name = 'delivery';
    }

    public function send_create() {
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;

        $data = ($params && isset($params['data'])) ? $params['data'] : array();

        return $this->get_params($data, $fields_map);
    }

    public function send_update() {
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;

        $data = ($params && isset($params['data'])) ? $params['data'] : array();

        return $this->get_params($data, $fields_map);
    }

    public function send_finish() {
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;

        $data = ($params && isset($params['data'])) ? $params['data'] : array();

        return $this->get_params($data, $fields_map);
    }

    public function reship_create() {
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;

        $data = ($params && isset($params['data'])) ? $params['data'] : array();

        return $this->get_params($data, $fields_map);
    }

    public function reship_update() {
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;

        $data = ($params && isset($params['data'])) ? $params['data'] : array();

        return $this->get_params($data, $fields_map);
    }

    public function reship_finish() {
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;

        $data = ($params && isset($params['data'])) ? $params['data'] : array();

        return $this->get_params($data, $fields_map);
    }

}
