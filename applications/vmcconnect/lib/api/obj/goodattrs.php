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

class vmcconnect_api_obj_goodattrs extends vmcconnect_api_obj_base {

    protected $_fields = 'type_id, name, params, use_props, use_params, assrule, disabled';
    protected $_get_fields = 'type_id, name, params, use_props, use_params, assrule, props, disabled';

    /*
     * 获取商品类型列表
     */
    public function read_get() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_all($fields, $params);

        // 合并到返回数据
        $res['total'] = $data ? count($data) : 0;
        $res['result'] = $data;
        // 返回数据
        return $res;
    }

    /*
     * 获取商品类型属性
     */
    public function read_valuesByAttrId() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($type_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'type_id'
            );
            return $res;
        }
        if (!is_numeric($type_id) || $type_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'type_id'
            );
            return $res;
        }

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_byId($type_id, $fields);

        // 合并到返回数据
        $res['result'] = $data;
        // 返回数据
        return $res;
    }

    //
    protected function _mod_type() {
        static $mod_goods_type;
        if ($mod_goods_type) return $mod_goods_type;
        $mod_goods_type = app::get('b2c')->model('goods_type');
        return $mod_goods_type;
    }

    protected function _mod_type_prop() {
        static $mod_goods_type_prop;
        if ($mod_goods_type_prop) return $mod_goods_type_prop;
        $mod_goods_type_prop = app::get('b2c')->model('goods_type_props');
        return $mod_goods_type_prop;
    }

    protected function _get_type_props($type_id) {

        $type_id = (int) $type_id;
        if (!$type_id) return false;

        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            return $return;
        }
        cachemgr::co_start();

        $_mod_type_prop = $this->_mod_type_prop();
        $_db = $_mod_type_prop->db;

        $rows = $_mod_type_prop->getList('*', array('type_id' => $type_id));
        if (!$rows) return false;

        $_prop_ids = array();
        foreach ($rows as $_k => $_v) {
            $_prop_ids[] = $_v['props_id'];
        }

        $_prop_values = array();
        $_tmp_rows = $_db->select("select * from `" . $_db->prefix . "b2c_goods_type_props_value` where props_id in ('" . implode("', '", $_prop_ids) . "')");
        if ($_tmp_rows) {
            foreach ($_tmp_rows as $_v) {
                $__props_id = $_v['props_id'];
                unset($_v['props_id']);
                $_prop_values[$__props_id][$_v['props_value_id']] = $_v;
            }
        }

        $props = array();
        foreach ($rows as $_v) {
            $_v['prop_values'] = ($_prop_values && isset($_prop_values[$_v['props_id']])) ? $_prop_values[$_v['props_id']] : null;
            $props[$_v['props_id']] = $_v;
        }

        cachemgr::set($cache_key, $props, cachemgr::co_end());
        return $props;
    }

    protected function _sort_types($rows) {
        if (!$rows || !is_array($rows)) return false;
        $all_types = array();
        foreach ($rows as $_k => $_v) {
            $use_props = false;
            $use_params = false;
            $assrule = null;
            if ($_v['setting']) {
                isset($_v['setting']['use_props']) && $_v['setting']['use_props'] && $use_props = true;
                isset($_v['setting']['use_params']) && $_v['setting']['use_params'] && $use_params = true;
                isset($_v['setting']['assrule']) && strlen($_v['setting']['assrule']) && $assrule = $_v['setting']['assrule'];
            }
            $_v['disabled'] = $_v['disabled'] == 'false' ? false : true;
            $_v['use_props'] = $use_props;
            $_v['use_params'] = $use_params;
            $_v['assrule'] = $assrule;
            unset($_v['setting']);
            $all_types[$_v['type_id']] = $_v;
        }
        return $all_types;
    }

    protected function _get_all_types($offset = 0, $limit = -1) {

        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            $all_types = $return;
            return $return;
        }
        cachemgr::co_start();

        $rows = $this->_mod_type()->getList('*', null, $offset, $limit);
        if (!$rows) return false;

        $all_types = $this->_sort_types($rows);
        cachemgr::set($cache_key, $all_types, cachemgr::co_end());

        return $all_types;
    }

    protected function _get_all($fields, $params) {

        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            $all_types = $return;
        }
        if (!$all_types) {
            cachemgr::co_start();

            $_page = (isset($params['page']) && $params['page']) ? $params['page'] : 1;
            $_pageSize = (isset($params['pageSize']) && $params['pageSize']) ? $params['pageSize'] : 20;
            (!$_page || $_page < 1) && $_page = 1;
            (!$_pageSize || $_pageSize < 1 || $_pageSize > 100) && $_pageSize = 20;

            $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;
            $all_types = $this->_get_all_types(($_page - 1) * $_pageSize, $_pageSize);
            cachemgr::set($cache_key, $all_types, cachemgr::co_end());
        }

        if (!$all_types) return false;
        $res = $this->_field_rows($fields, $all_types);
        return $res;
    }

    protected function _get_type($type_id) {
        $type_id = (int) $type_id;
        if (!$type_id) return false;

        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            return $return;
        }
        cachemgr::co_start();

        $row = $this->_mod_type()->getRow('*', array('type_id' => $type_id));
        if (!$row) return false;

        $use_props = false;
        $use_params = false;
        $assrule = null;
        if ($row['setting']) {
            isset($row['setting']['use_props']) && $row['setting']['use_props'] && $use_props = true;
            isset($row['setting']['use_params']) && $row['setting']['use_params'] && $use_params = true;
            isset($row['setting']['assrule']) && strlen($row['setting']['assrule']) && $assrule = $row['setting']['assrule'];
        }
        $row['disabled'] = $row['disabled'] == 'false' ? false : true;
        $row['use_props'] = $use_props;
        $row['use_params'] = $use_params;
        $row['assrule'] = $assrule;
        unset($row['setting']);

        $row['props'] = $this->_get_type_props($type_id);
        cachemgr::set($cache_key, $row, cachemgr::co_end());

        return $row;
    }

    protected function _get_byId($type_id, $fields) {
        $type = $this->_get_type($type_id);
        if (!$type) return false;

        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_get_fields;
        $res = $this->_field_row($fields, $type);
        return $res;
    }

}
