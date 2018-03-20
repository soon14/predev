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

class vmcconnect_object_api_output_def_system extends vmcconnect_object_api_output_def_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->pack_name = 'system';
    }

    // system.ping - PING 
    public function ping() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // system.read.setting.info - 查询基本信息 
    public function read_setting_info() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // system.read.setting.pc - 查询PC版基本信息 
    public function read_setting_pc() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // system.read.setting.mobile - 查询手机版基本信息 
    public function read_setting_mobile() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // system.returnaddress.read.get - 查询退货地址列表 
    public function returnaddress_read_get() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // system.returnaddress.read.getdef - 查询默认退货地址 
    public function returnaddress_read_getdef() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // system.shipaddress.read.get - 查询发货地址列 
    public function shipaddress_read_get() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // system.shipaddress.read.getdef - 查询默认发货地址 
    public function shipaddress_read_getdef() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

}
