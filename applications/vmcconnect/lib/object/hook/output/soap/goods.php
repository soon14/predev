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

class vmcconnect_object_hook_output_soap_goods extends vmcconnect_object_hook_output_soap_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->_name = 'goods';
    }

    public function create() {
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;
        $goods = $params && isset($params['data']) ? $params['data'] : null;
        $filter = $params && isset($params['filter']) ? $params['filter'] : null;
        $data_goods = $goods ? $this->get_params($goods, $fields_map) : null;
        $data_goods['goods_setting'] = !is_array($data_goods['goods_setting']) ? unserialize($data_goods['goods_setting']) : $data_goods['goods_setting'];
        $data_goods['spec_desc'] = !is_array($data_goods['spec_desc']) ? unserialize($data_goods['spec_desc']) : $data_goods['spec_desc'];
        return $data_goods;
    }

    public function update() {
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;

        $goods = $params && isset($params['data']) ? $params['data'] : null;
        $filter = $params && isset($params['filter']) ? $params['filter'] : null;
        $data_goods = $goods ? $this->get_params($goods, ($fields_map && isset($fields_map['goods']) ? $fields_map['goods'] : null)) : null;
        $data_filter = $filter ? $this->get_params($filter, ($fields_map && isset($fields_map['filter']) ? $fields_map['filter'] : null)) : null;
        $res = array(
            'goods' => $data_goods,
            'filter' => $data_filter,
        );
        return $res;
    }

    public function delete() {
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;
        $filter = $params && isset($params['filter']) ? $params['filter'] : null;
        $data_filter = $filter ? $this->get_params($filter, ($fields_map && isset($fields_map) ? $fields_map : null)) : null;
        $res = array(
            'filter' => $data_filter,
        );
        return $res;
    }

}
