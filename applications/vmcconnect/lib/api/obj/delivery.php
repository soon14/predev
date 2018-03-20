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

class vmcconnect_api_obj_delivery extends vmcconnect_api_obj_base {

    protected $_fields = 'corp_id, corp_code, name, website, request_url, ordernum';

    /*
     * 添加物流公司
     */
    public function write_add() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);
        if (!isset($name)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'name'
            );
            return $res;
        }
        if (!isset($corp_code)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'corp_code'
            );
            return $res;
        }
        if (!strlen($name)) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'name'
            );
            return $res;
        }
        if (!strlen($corp_code)) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'corp_code'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_save_dlycorp($params);
        if (!$data) {
            $res['code'] = 43;
            return $res;
        }
        $data['create_time'] = date('Y-m-d', ($data['create_time'] ? $data['create_time'] : time()));

        // 合并到返回数据
        $res['result'] = $data;
        // 返回数据
        return $res;
    }

    /*
     * 添加物流公司
     */
    public function write_edit() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);
        if (!isset($corp_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'corp_id'
            );
            return $res;
        }
        if (!isset($name)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'name'
            );
            return $res;
        }
        if (!isset($corp_code)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'corp_code'
            );
            return $res;
        }
        if (!$corp_id) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'corp_id'
            );
            return $res;
        }
        if (!strlen($name)) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'name'
            );
            return $res;
        }
        if (!strlen($corp_code)) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'corp_code'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_save_dlycorp($params);
        if (!$data) {
            $res['code'] = 43;
            return $res;
        }
        $data['modified'] = date('Y-m-d', ($data['modified'] ? $data['modified'] : time()));
        // 合并到返回数据
        $res['result'] = $data;
        // 返回数据
        return $res;
    }

    /*
     * 删除物流公司
     */
    public function write_delete() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);
        if (!isset($corp_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'corp_id'
            );
            return $res;
        }
        if (!$corp_id) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'corp_id'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_remove_dlycorp($corp_id);
        if (!$data) {
            $res['code'] = 43;
            return $res;
        }

        $data['created'] = date('Y-m-d', ($data['created'] ? $data['created'] : time()));
        // 合并到返回数据
        $res['result'] = $data;
        // 返回数据
        return $res;
    }

    /*
     * 获取物流公司
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
        $data = $this->_get_all($fields);

        // 合并到返回数据
        $res['total'] = $data ? count($data) : 0;
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    //
    protected function _mod_dlycorp() {
        static $mod_dlycorp;
        if ($mod_dlycorp) return $mod_dlycorp;
        $mod_dlycorp = app::get('b2c')->model('dlycorp');
        return $mod_dlycorp;
    }

    protected function _sort_dlycorps($rows) {
        if (!$rows || !is_array($rows)) return false;
        $all_dlycorps = array();
        foreach ($rows as $_k => $_v) {
            $all_dlycorps[$_v['corp_id']] = $_v;
        }
        return $all_dlycorps;
    }

    protected function _get_all_dlycorps() {
        static $all_dlycorps;
        if ($all_dlycorps) return $all_dlycorps;

        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            $all_dlycorps = $return;
            return $return;
        }
        cachemgr::co_start();

        $rows = $this->_mod_dlycorp()->getList('*', array(
            'disabled' => 'false',
        ));
        if (!$rows) return false;

        $all_dlycorps = $this->_sort_dlycorps($rows);
        cachemgr::set($cache_key, $all_dlycorps, cachemgr::co_end());

        return $all_dlycorps;
    }

    protected function _get_all($fields) {
        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;
        $all_dlycorps = $this->_get_all_dlycorps();
        if (!$all_dlycorps) return false;

        $res = $this->_field_rows($fields, $all_dlycorps);
        return $res;
    }

    protected function _save_dlycorp($params) {

        $is_update = (isset($params['corp_id']) && $params['corp_id']) ? true : false;
        $datas = array();
        $is_update && $datas['corp_id'] = (int) $params['corp_id'];

        isset($params['ordernum']) && $datas['ordernum'] = (int) $params['ordernum'];
        isset($params['website']) && $datas['website'] = trim($params['website']);
        isset($params['request_url']) && $datas['request_url'] = trim($params['request_url']);

        $datas['name'] = trim($params['name']);
        $datas['corp_code'] = trim($params['corp_code']);

        $_mod_dlycorp = $this->_mod_dlycorp();

        $this->_begin();

        $res = $_mod_dlycorp->save($datas);
        if (!$res || !isset($datas['corp_id']) || !$datas['corp_id']) {
            $this->_end(false);
            return false;
        }
        $new_id = $datas['corp_id'];

        $res_data = array();
        !$is_update && $res_data['create_time'] = time();
        $is_update && $res_data['modified'] = time();
        $res_data['corp_id'] = $new_id;

        $this->_end(true);

        return $res_data;
    }

    protected function _remove_dlycorp($corp_id) {
        $corp_id = (int) $corp_id;
        if (!$corp_id) return false;

        $_mod_dlycorp = $this->_mod_dlycorp();

        $res_data = array();

        $this->_begin();

        if (!$_mod_dlycorp->delete(array('corp_id' => $corp_id))) {
            $this->_end(false);
            return false;
        }

        $this->_end(true);

        $res_data['created'] = time();

        return $res_data;
    }

}
