<?php

/**
 * Created by PhpStorm.
 * User: Ganxiaohui
 * Date: 2017/6/22
 * Time: 19:25
 */
class vmcconnect_api_obj_distribution extends vmcconnect_api_obj_base {

    //返回数组的字段
    protected $_fields = 'real_type, type';
    /*
     * 查询配送方式
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

    //比对返回字段与数组字段一致性
    protected function _get_all($fields) {
        $fields = (strlen($fields) && trim($fields) != '*') ? $fields : $this->_fields;
        $all_distributions = $this->_get_all_distributions();
        if (!$all_distributions) return false;
        //此处得到了数组
        //改造一下
        $arr = array();
        $i = 1;
        foreach ($all_distributions as $k=>$v) {
            $arr[$i]['real_type'] = $k;
            $arr[$i]['type'] = $v;
            $i++;
        }
        $res = $this->_field_rows($fields, $arr);
        return $res;
    }
    
    protected function _get_all_distributions() {
        static $all_distributions;
        if ($all_distributions) return $all_distributions;

        $_key_array = func_get_args();
        array_push($_key_array, __METHOD__);
        $cache_key = utils::array_md5($_key_array);
        if (cachemgr::get($cache_key, $return)) {
            $all_distributions = $return;
            return $return;
        }
        cachemgr::co_start();

        $delivery_schema = app::get('b2c')->model('delivery')->get_schema();
        $rows = $delivery_schema['columns']['send_router']['type'];

        if (!$rows) return false;

        $all_distributions = $rows;
        cachemgr::set($cache_key, $all_distributions, cachemgr::co_end());

        return $all_distributions;
    }
}