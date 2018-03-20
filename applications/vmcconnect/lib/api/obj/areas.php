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

class vmcconnect_api_obj_areas extends vmcconnect_api_obj_base {

    protected $_fields = 'region_id, local_name, package, p_region_id, region_path, ordernum';

    /*
     * 获取省级地址列表
     */
    public function read_province_get() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_provinces($fields);

        // 合并到返回数据
        $res['total'] = $data ? count($data) : 0;
        $res['result'] = $data;

        // 合并到返回数据
        $res['result'] = $data;
        // 返回数据
        return $res;
    }

    /*
     * 获取市级信息列表
     */
    public function read_city_get() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($parent_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'parent_id'
            );
            return $res;
        }
        if (!is_numeric($parent_id) || $parent_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'parent_id'
            );
            return $res;
        }

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_children($parent_id, 2, $fields);

        // 合并到返回数据
        $res['total'] = $data ? count($data) : 0;
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 获取区县级信息列表
     */
    public function read_county_get() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($parent_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'parent_id'
            );
            return $res;
        }
        if (!is_numeric($parent_id) || $parent_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'parent_id'
            );
            return $res;
        }

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_children($parent_id, 3, $fields);

        // 合并到返回数据
        $res['total'] = $data ? count($data) : 0;
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 获取乡镇级信息列表
     */
    public function read_town_get() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($parent_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'parent_id'
            );
            return $res;
        }
        if (!is_numeric($parent_id) || $parent_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'parent_id'
            );
            return $res;
        }

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_children($parent_id, 4, $fields);

        // 合并到返回数据
        $res['total'] = $data ? count($data) : 0;
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    // ---------------------------------------
    protected function _mod_regions() {
        static $_mod_regions;
        if ($_mod_regions) return $_mod_regions;
        $_mod_regions = app::get('ectools')->model('regions');
        return $_mod_regions;
    }

    protected function _sort_regions($rows) {
        if (!$rows || !is_array($rows)) return false;
        $all_regions = array();
        foreach ($rows as $_k => $_v) {
            $all_regions[$_v['region_id']] = $_v;
        }
        return $all_regions;
    }

    protected function _provinces() {
        static $all_provinces;
        if ($all_provinces) return $all_provinces;

        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            $all_provinces = $return;
            return $return;
        }
        cachemgr::co_start();

        $rows = $this->_mod_regions()->getList('*', array(
            'region_grade' => 1,
            'disabled' => 'false',
                ), 0, -1, 'ordernum desc,region_id asc');
        if (!$rows) return false;

        $all_provinces = $this->_sort_regions($rows);
        cachemgr::set($cache_key, $all_provinces, cachemgr::co_end());

        return $all_provinces;
    }

    protected function _get_provinces($fields) {
        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;
        $all_provinces = $this->_provinces();
        if (!$all_provinces) return false;

        $res = $this->_field_rows($fields, $all_provinces);
        return $res;
    }

    protected function _children($parent_id, $grade) {
        $parent_id = (int) $parent_id;
        $grade = (int) $grade;
        if (!$parent_id || !$grade) return false;

        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            return $return;
        }
        cachemgr::co_start();

        $rows = $this->_mod_regions()->getList('*', array(
            'p_region_id' => $parent_id,
            'region_grade' => $grade,
            'disabled' => 'false',
                ), 0, -1, 'ordernum desc,region_id asc');
        if (!$rows) return false;

        $all_children = $this->_sort_regions($rows);
        cachemgr::set($cache_key, $all_children, cachemgr::co_end());

        return $all_children;
    }

    protected function _get_children($parent_id, $grade, $fields = null) {
        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;
        $all_children = $this->_children($parent_id, $grade);
        if (!$all_children) return false;

        $res = $this->_field_rows($fields, $all_children);
        return $res;
    }

}
