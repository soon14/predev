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

class vmcconnect_logs_base {

    public function __construct() {
        $this->objlog = vmc::singleton('operatorlog_service_desktop_controller');
        $this->delimiter = vmc::singleton('operatorlog_service_desktop_controller')->get_delimiter();
    }

    public function logs($module, $operate_type, $memo) {
        $obj = new desktop_user();
        $data['username'] = ($obj->get_login_name()) ? ($obj->get_login_name()) : 'system_core';
        $data['module'] = $module;
        $data['operate_type'] = $operate_type;
        $data['dateline'] = time();
        $data['memo'] = $memo;
        app::get('operatorlog')->model('normallogs')->insert($data);
    }

    function save($module, $operate_type, $memo) {
        $obj = new desktop_user();
        $data['username'] = ($obj->get_login_name()) ? ($obj->get_login_name()) : 'system_core';
        $data['module'] = $module;
        $data['operate_type'] = $operate_type;
        $data['dateline'] = time();
        $data['memo'] = $memo;
        app::get('operatorlog')->model('normallogs')->insert($data);
    }

}
