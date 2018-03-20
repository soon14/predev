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

class vmcconnect_api_obj_promotion extends vmcconnect_api_obj_base {
    /*
     * 创建促销
     */
    public function write_add() {
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
     * 设置参加促销的sku
     */
    public function write_sku() {
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
     * 暂停促销
     */
    public function write_suspend() {
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
     * 删除促销
     */
    public function write_delete() {
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
     * 添加订单规则
     */
    public function ordermode_write_add() {
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
     * 根据促销编号获取促销的订单规则
     */
    public function ordermode_read_list() {
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
     * 根据促销编号获取促销的活动规则
     */
    public function read_activitymode_get() {
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
     * 根据促销编号获取促销详细信息
     */
    public function read_get() {
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
     * 促销列表查询接口
     */
    public function read_list() {
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
