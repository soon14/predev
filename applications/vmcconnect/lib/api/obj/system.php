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

class vmcconnect_api_obj_system extends vmcconnect_api_obj_base {

    protected $_fields = 'dp_id, dp_no, dp_type, dp_type_var, dp_title, consignor_name, consignor_area, consignor_addr, consignor_zip, consignor_tel, consignor_mobile, consignor_email, memo, is_default, is_default_reship';
    protected $dp_type_vars = array(
        'warehouse' => '仓库',
        'store' => '门店',
        'tpwarehouse' => '第三方仓库',
    );

    /*
     * 查询基本信息
     */
    public function ping() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        $msg = isset($msg) ? $msg : 'ping';
        // 返回数据
        $data = array();
        $data['msg'] = $msg;
        // 合并到返回数据
        $res['result'] = $data;
        // 返回数据
        return $res;
    }

    /*
     * 查询基本信息
     */
    public function read_setting_info() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // 返回数据
        $data = $this->_get_sys_info();

        // 合并到返回数据
        $res['result'] = $data;
        // 返回数据
        return $res;
    }

    /*
     * 查询PC版基本信息
     */
    public function read_setting_pc() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // 返回数据
        $data = $this->_get_pc_info();

        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 查询手机版基本信息
     */
    public function read_setting_mobile() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // 返回数据
        $data = $this->_get_mobile_info();

        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 查询退货地址列表
     */
    public function returnaddress_read_get() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_returnaddress($fields);

        // 合并到返回数据
        $res['total'] = $data ? count($data) : 0;
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 查询默认退货地址
     */
    public function returnaddress_read_getdef() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);
        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_returnaddress_def($fields);

        // 合并到返回数据
        $res['result'] = $data;
        // 返回数据
        return $res;
    }

    /*
     * 查询发货地址列
     */
    public function shipaddress_read_get() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_shipaddress($fields);

        // 合并到返回数据
        $res['total'] = $data ? count($data) : 0;
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 查询默认发货地址
     */
    public function shipaddress_read_getdef() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);
        // 返回数据
        $data = array();
        $data['msg'] = 'ping';
        // 合并到返回数据
        $res['result'] = $data;
        // 返回数据
        return $res;
    }

    // ----------------------------
    protected function _get_sys_info() {
        static $_sys_info;
        if ($_sys_info) return $_sys_info;
        $conf = include app::get('b2c')->app_dir . '/setting.php';
        if (!$setting) return false;
        $_sys_info = array();
        foreach ($setting as $_k => $_v) {
            if (!isset($_v['desc']) || !strlen($_v['desc'])) continue;
            $_value = isset($_v['value']) ? $_v['value'] : (isset($_v['default']) ? $_v['default'] : null);
            $_var = $_value;
            if (
                    $_value &&
                    isset($_v['options']) &&
                    is_array($_v['options']) &&
                    isset($_v['options'][$_value])
            ) {
                $_var = $_v['options'][$_value];
            }
            $_sys_info[$_k] = array(
                'desc' => $_v['desc'],
                'value' => $_value,
                'var' => $_var,
            );
        }
        return $_sys_info;
    }

    protected function _get_pc_info() {
        static $_sys_info;
        if ($_sys_info) return $_sys_info;
        $conf = include app::get('site')->app_dir . '/setting.php';
        if (!$setting) return false;
        $_sys_info = array();
        foreach ($setting as $_k => $_v) {
            if (!isset($_v['desc']) || !strlen($_v['desc'])) continue;
            $_value = isset($_v['value']) ? $_v['value'] : (isset($_v['default']) ? $_v['default'] : null);
            $_var = $_value;
            if (
                    $_value &&
                    isset($_v['options']) &&
                    is_array($_v['options']) &&
                    isset($_v['options'][$_value])
            ) {
                $_var = $_v['options'][$_value];
            }
            $_sys_info[$_k] = array(
                'desc' => $_v['desc'],
                'value' => $_value,
                'var' => $_var,
            );
        }
        return $_sys_info;
    }

    protected function _get_mobile_info() {
        static $_sys_info;
        if ($_sys_info) return $_sys_info;
        $conf = include app::get('mobile')->app_dir . '/setting.php';
        if (!$setting) return false;
        $_sys_info = array();
        foreach ($setting as $_k => $_v) {
            if (!isset($_v['desc']) || !strlen($_v['desc'])) continue;
            $_value = isset($_v['value']) ? $_v['value'] : (isset($_v['default']) ? $_v['default'] : null);
            $_var = $_value;
            if (
                    $_value &&
                    isset($_v['options']) &&
                    is_array($_v['options']) &&
                    isset($_v['options'][$_value])
            ) {
                $_var = $_v['options'][$_value];
            }
            $_sys_info[$_k] = array(
                'desc' => $_v['desc'],
                'value' => $_value,
                'var' => $_var,
            );
        }
        return $_sys_info;
    }

    protected function _mod_dlyplace() {
        static $mod_dlyplace;
        if ($mod_dlyplace) return $mod_dlyplace;
        $mod_dlyplace = app::get('b2c')->model('dlyplace');
        return $mod_dlyplace;
    }

    protected function _sort_address($rows) {
        if (!$rows || !is_array($rows)) return false;
        $all_address = array();
        foreach ($rows as $_k => $_v) {
            $_v['dp_type_var'] = ($_v['dp_type'] && isset($this->dp_type_vars[$_v['dp_type']])) ? $this->dp_type_vars[$_v['dp_type']] : null;
            $all_address[$_v['dp_id']] = $_v;
        }
        return $all_address;
    }

    protected function _get_all_address() {
        static $all_address;
        if ($all_address) return $all_address;

        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            $all_address = $return;
            return $return;
        }
        cachemgr::co_start();

        $rows = $this->_mod_dlyplace()->getList('*', array(
            'disabled' => 'false',
        ));
        if (!$rows) return false;

        $all_address = $this->_sort_address($rows);
        cachemgr::set($cache_key, $all_address, cachemgr::co_end());

        return $all_address;
    }

    protected function _get_returnaddress($fields) {
        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;
        $all_address = $this->_get_all_address();
        if (!$all_address) return false;

        $res = $this->_field_rows($fields, $all_address);
        return $res;
    }

    protected function _returnaddress_def() {
        static $returnaddress_def;
        if ($returnaddress_def) return $returnaddress_def;

        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            $returnaddress_def = $return;
            return $return;
        }
        cachemgr::co_start();

        $row = $this->_mod_dlyplace()->getRow('*', array(
            'is_default_reship' => 'true',
            'disabled' => 'false',
        ));
        if (!$row) return false;

        $row['dp_type_var'] = ($row['dp_type'] && isset($this->dp_type_vars[$row['dp_type']])) ? $this->dp_type_vars[$row['dp_type']] : null;

        $returnaddress_def = $row;
        cachemgr::set($cache_key, $returnaddress_def, cachemgr::co_end());
        return $returnaddress_def;
    }

    protected function _get_returnaddress_def($fields) {
        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;
        $def_address = $this->_returnaddress_def();
        if (!$def_address) return false;

        $res = $this->_field_row($fields, $def_address);
        return $res;
    }

    protected function _get_shipaddress($fields) {
        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;
        $all_address = $this->_get_all_address();
        if (!$all_address) return false;

        $res = $this->_field_rows($fields, $all_address);
        return $res;
    }

    protected function _shipaddress_def() {
        static $shipaddress_def;
        if ($shipaddress_def) return $shipaddress_def;

        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            $shipaddress_def = $return;
            return $return;
        }
        cachemgr::co_start();

        $row = $this->_mod_dlyplace()->getRow('*', array(
            'is_default' => 'true',
            'disabled' => 'false',
        ));
        if (!$row) return false;

        $row['dp_type_var'] = ($row['dp_type'] && isset($this->dp_type_vars[$row['dp_type']])) ? $this->dp_type_vars[$row['dp_type']] : null;

        $shipaddress_def = $row;
        cachemgr::set($cache_key, $shipaddress_def, cachemgr::co_end());

        return $shipaddress_def;
    }

    protected function _get_shipaddress_def($fields) {
        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;
        $def_address = $this->_shipaddress_def();
        if (!$def_address) return false;

        $res = $this->_field_row($fields, $def_address);
        return $res;
    }

}
