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

class vmcconnect_api_obj_coupon extends vmcconnect_api_obj_base {
    /*
     * 新建优惠券
     */
    public function write_create() {
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

    /*
     * 关闭优惠券
     */
    public function write_close() {
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

    /*
     * 根据优惠券编号查询优惠券
     */
    public function read_getById() {
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

    /*
     * 根据指定条件(分页)查询创建的优惠券
     */
    public function read_getList() {
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

    /*
     * 统计满足条件的优惠券个数
     */
    public function read_getCount() {
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

}
