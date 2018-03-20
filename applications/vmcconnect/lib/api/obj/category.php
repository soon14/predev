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

class vmcconnect_api_obj_category extends vmcconnect_api_obj_base {

    protected $_fields = 'cat_id, parent_id, cat_path, has_children, cat_name, p_order, visible, icon, last_modify';

    /*
     * 添加分类
     */
    public function write_add() {
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
        if (!isset($cat_name)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'cat_name'
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
        if (!strlen($cat_name)) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'cat_name'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_save_cat($params);
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
     * 删除分类
     */
    public function write_delete() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);
        
        if (!isset($cat_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'cat_id'
            );
            return $res;
        }
        if (!is_numeric($cat_id) || $cat_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'cat_id'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_remove_cat($cat_id);
        if (!$data) {
            $res['code'] = 43;
            return $res;
        }
        if ($data['code']) {
            $res['code'] = $data['code'];
            return $res;
        }

        $data['modified'] = date('Y-m-d', ($data['modified'] ? $data['modified'] : time()));
        // 合并到返回数据
        $res['result'] = $data;
        // 返回数据
        return $res;
    }

    /*
     * 更新分类
     */
    public function write_update() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($cat_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'cat_id'
            );
            return $res;
        }
//        if (!isset($parent_id)) {
//            $res['code'] = 60;
//            $res['msg_strs'] = array(
//                'parent_id'
//            );
//            return $res;
//        }
        if (!isset($cat_name)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'cat_name'
            );
            return $res;
        }
        if (!is_numeric($cat_id) || $cat_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'cat_id'
            );
            return $res;
        }
//        if (!is_numeric($parent_id) || $parent_id < 0) {
//            $res['code'] = 61;
//            $res['msg_strs'] = array(
//                'parent_id'
//            );
//            return $res;
//        }
        if (!strlen($cat_name)) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'cat_name'
            );
            return $res;
        }

        // 返回数据
        $data = $this->_save_cat($params);
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
     * 获取所有类目信息
     */
    public function read_getAll() {
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
     * 获取前台展示的分类
     */
    public function read_getFront() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_front($fields, $params);

        // 合并到返回数据
        $res['total'] = $data ? count($data) : 0;
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 获取单个类目信息
     */
    public function read_findById() {
        // 返回值初始化
        $res = $this->base_res;
        // 传入参数初始化
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        $params && is_array($params) && extract($params);

        if (!isset($cat_id)) {
            $res['code'] = 60;
            $res['msg_strs'] = array(
                'cat_id'
            );
            return $res;
        }
        if (!is_numeric($cat_id) || $cat_id < 0) {
            $res['code'] = 61;
            $res['msg_strs'] = array(
                'cat_id'
            );
            return $res;
        }

        // 返回数据
        $fields = isset($fields) ? $fields : '*';
        $data = $this->_get_byId($cat_id, $fields);

        // 合并到返回数据
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    /*
     * 查找子类目列表
     */
    public function read_findByPId() {
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
        $data = $this->_get_byPid($parent_id, $fields, $params);

        // 合并到返回数据
        $res['total'] = $data ? count($data) : 0;
        $res['result'] = $data;

        // 返回数据
        return $res;
    }

    // --------------------------------------------------- 相关方法
    protected function _mod_cat() {
        static $mod_goods_cat;
        if ($mod_goods_cat) return $mod_goods_cat;
        $mod_goods_cat = app::get('b2c')->model('goods_cat');
        return $mod_goods_cat;
    }

    protected function _mod_goods() {
        static $mod_goods;
        if ($mod_goods) return $mod_goods;
        $mod_goods = app::get('b2c')->model('goods');
        return $mod_goods;
    }

    protected function _sort_cats($rows) {
        if (!$rows || !is_array($rows)) return false;
        $all_cats = array();
        foreach ($rows as $_k => $_v) {
            $visible = true;
            $icon = null;
            if ($_v['addon']) {
                isset($_v['addon']['visible']) && $_v['addon']['visible'] == 'false' && $visible = false;
                isset($_v['addon']['icon']) && strlen($_v['addon']['icon']) == 'false' && $icon = $_v['addon']['icon'];
            }
            $_v['cat_lev'] = count(explode(',', $_v['cat_path']));
            $_v['has_children'] = $_v['has_children'] == 'false' ? false : true;
            $_v['visible'] = $visible;
            $_v['icon'] = $icon;
            unset($_v['addon']);
            $all_cats[$_v['cat_id']] = $_v;
        }
        return $all_cats;
    }

    protected function _get_all_cats($filter = array(), $offset = 0, $limit = -1) {
        static $all_cats;
        if ($all_cats) return $all_cats;
        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            $all_cats = $return;
            return $return;
        }
        cachemgr::co_start();


        !$filter && $filter = array();
        $filter['disabled'] = 'false';
        $rows = $this->_mod_cat()->getList('*', $filter, $offset, $limit);
        if (!$rows) return false;

        $all_cats = $this->_sort_cats($rows);
        cachemgr::set($cache_key, $all_cats, cachemgr::co_end());

        return $all_cats;
    }

    protected function _get_all($fields, $params = array()) {

        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;

        $_page = (isset($params['page']) && $params['page']) ? $params['page'] : 1;
        $_pageSize = (isset($params['pageSize']) && $params['pageSize']) ? $params['pageSize'] : 20;
        (!$_page || $_page < 1) && $_page = 1;
        (!$_pageSize || $_pageSize < 1 || $_pageSize > 100) && $_pageSize = 20;

        $all_cats = $this->_get_all_cats(null, ($_page - 1) * $_pageSize, $_pageSize);
        if (!$all_cats) return false;
        $res = $this->_field_rows($fields, $all_cats);
        return $res;
    }

    protected function _sort_front_cats($all_cats, &$front_cats, $pid = 0) {
        static $all_cat_arrs;
        if (!$all_cat_arrs && $all_cats) {
            $all_cat_arrs = array();
            foreach ($all_cats as $_v) {
                $all_cat_arrs[$_v['parent_id']][$_v['cat_id']] = $_v;
            }
        }
        $pid = (int) $pid;
        $curr_cat = $pid ? (isset($all_cats[$pid]) ? $all_cats[$pid] : null) : null;
        $child_cat = ($all_cat_arrs && isset($all_cat_arrs[$pid])) ? $all_cat_arrs[$pid] : null;
        $show = ((!$pid || ($curr_cat && $curr_cat['visible'])) && $child_cat) ? true : false;
        if (!$show) return;
        foreach ($child_cat as $_v) {
            if (!$_v['visible']) continue;
            $front_cats[$_v['cat_id']] = $_v;
            $this->_sort_front_cats($all_cats, $front_cats, $_v['cat_id']);
        }
    }

    protected function _get_all_front_cats() {
        static $all_front_cats;

        if (!$all_front_cats) {
            $_all_cats = $this->_get_all_cats();
            if (!$_all_cats) return false;

            $all_front_cats = array();
            $this->_sort_front_cats($_all_cats, $all_front_cats);
        }
        if (!$all_front_cats) return false;

        return $all_front_cats;
    }

    protected function _get_front($fields, $params = array()) {
        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;

        $_page = (isset($params['page']) && $params['page']) ? $params['page'] : 1;
        $_pageSize = (isset($params['pageSize']) && $params['pageSize']) ? $params['pageSize'] : 20;
        (!$_page || $_page < 1) && $_page = 1;
        (!$_pageSize || $_pageSize < 1 || $_pageSize > 100) && $_pageSize = 20;

        $_all_front_cats = $this->_get_all_front_cats(($_page - 1) * $_pageSize, $_pageSize);
        if (!$_all_front_cats) return false;
        $_rows = array_slice($_all_front_cats, ($_page - 1) * $_pageSize, $_pageSize);
        $res = $_rows ? $this->_field_rows($fields, $_rows) : array();
        return $res;
    }

    protected function _get_cat($cat_id) {
        $cat_id = (int) $cat_id;
        if (!$cat_id) return false;

        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            return $return;
        }
        cachemgr::co_start();

        $row = $this->_mod_cat()->getRow('*', array(
            'cat_id' => $cat_id,
            'disabled' => 'false',
        ));
        if (!$row) return false;

        $visible = true;
        $icon = null;
        if ($row['addon']) {
            isset($row['addon']['visible']) && $row['addon']['visible'] == 'false' && $visible = false;
            isset($row['addon']['icon']) && strlen($row['addon']['icon']) == 'false' && $icon = $row['addon']['icon'];
        }

        $row['cat_lev'] = count(explode(',', $row['cat_path']));
        $row['has_children'] = $row['has_children'] == 'false' ? false : true;
        $row['visible'] = $visible;
        $row['icon'] = $icon;

        unset($row['addon']);
        cachemgr::set($cache_key, $row, cachemgr::co_end());

        return $row;
    }

    protected function _get_byId($cat_id, $fields) {
        $cat = $this->_get_cat($cat_id);
        if (!$cat) return false;

        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;
        $res = $this->_field_row($fields, $cat);
        return $res;
    }

    protected function _get_child_cats($parent_id, $offset = 0, $limit = -1) {
        $parent_id = (int) $parent_id;
        if (!$parent_id) return false;

        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);

        if (cachemgr::get($cache_key, $return)) {
            return $return;
        }
        cachemgr::co_start();

        $rows = $this->_mod_cat()->getList('*', array(
            'parent_id' => $parent_id,
            'disabled' => 'false',
                ), $offset, $limit);

        if (!$rows) return false;

        $all_cats = $this->_sort_cats($rows);
        cachemgr::set($cache_key, $all_cats, cachemgr::co_end());

        return $all_cats;
    }

    protected function _get_byPid($parent_id, $fields, $params) {
        $_page = (isset($params['page']) && $params['page']) ? $params['page'] : 1;
        $_pageSize = (isset($params['pageSize']) && $params['pageSize']) ? $params['pageSize'] : 20;
        (!$_page || $_page < 1) && $_page = 1;
        (!$_pageSize || $_pageSize < 1 || $_pageSize > 100) && $_pageSize = 20;

        $cats = $this->_get_child_cats($parent_id, ($_page - 1) * $_pageSize, $_pageSize);
        if (!$cats) return false;

        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;
        $res = $this->_field_rows($fields, $cats);
        return $res;
    }

    protected function _save_cat($params) {
        
        $is_update = (isset($params['cat_id']) && $params['cat_id']) ? true : false;

        $oldData = $is_update ? $this->_mod_cat()->dump($params['cat_id']) : null;
        
        $_parent_id = ($is_update) ? $oldData['parent_id'] : (int) $params['parent_id'];
        
        $datas = array();
        $is_update && $datas['cat_id'] = (int) $params['cat_id'];
        $datas['parent_id'] = $_parent_id;
        $datas['cat_name'] = trim($params['cat_name']);
        isset($params['p_order']) && $datas['p_order'] = (int) $params['p_order'];

        $addon = array();
        $oldData && $oldData['addon'] && $addon = $oldData['addon'];
        (isset($params['visible']) && (
                (is_bool($params['visible']) && $params['visible'] === false) ||
                (!is_bool($params['visible']) && $params['visible'] == 'false')
                )
                ) && $addon['visible'] = 'false';
        $datas['addon'] = $addon;

        $_mod_cat = $this->_mod_cat();

        $path = array();

        $parent_id = $datas['parent_id'];

        $this->_begin();

        $res = $_mod_cat->save($datas);
        if (!$res || !isset($datas['cat_id']) || !$datas['cat_id']) {
            $this->_end(false);
            return false;
        }
        $new_cat_id = $datas['cat_id'];

        $res_data = array();
        !$is_update && $res_data['create_time'] = time();
        $is_update && $res_data['modified'] = time();
        $res_data['cat_id'] = $new_cat_id;

        $_mod_cat->update_path($new_cat_id);
        $_mod_cat->update_path($parent_id);
        $_mod_cat->update_children_path($new_cat_id);
        $_mod_cat->clean_cache();

        $this->_end(true);

        return $res_data;
    }

    protected function _remove_cat($cat_id) {
        $cat_id = (int) $cat_id;
        if (!$cat_id) return false;

        $_mod_cat = $this->_mod_cat();

        $res_data = array();
        $res_data['cat_id'] = $cat_id;
        $res_data['code'] = null;

        if ($_mod_cat->count(array('parent_id' => $cat_id))) {
            $res_data['code'] = 21201;
            return $res_data;
        }

        if ($this->_mod_goods()->count(array('cat_id' => $cat_id))) {
            $res_data['code'] = 21202;
            return $res_data;
        }

        $cat_parent = $_mod_cat->getRow('parent_id', array(
            'cat_id' => intval($cat_id),
        ));

        $this->_begin();

        if (!$_mod_cat->delete(array('cat_id' => $cat_id))) {
            $this->_end(false);
            return false;
        }
        unset($res_data['code']);

        //更新父类路径
        $_mod_cat->update_path($cat_parent['parent_id']);
        $_mod_cat->clean_cache();

        $this->_end(true);

        $res_data['modified'] = time();

        return $res_data;
    }

}
