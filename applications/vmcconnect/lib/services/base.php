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

class vmcconnect_services_base {

    public function __construct($app) {
        $this->app = $app;
    }

    public function save_task($task_type, $task_data) {
        if(!$task_type) return false;
        $data = array();
        $data['task_type'] = $task_type;
        $data['task_data'] = serialize($task_data);
        $res = $this->app->model('hooktasks')->save($data);
        if(!$res) return false;
        return $data;
    }
    
    protected function _get_task_rows($hook_name) {

        $hook_name = trim($hook_name);
        if (!$hook_name) return false;

        $_sql = "select app.app_id, app.app_secret, hook.hook_id, hook.hook_url, hook.hook_addon, hook.hook_msg_tpl "
            . " from " . vmc::database()->prefix . "vmcconnect_apps app "
            . " join " . vmc::database()->prefix . "vmcconnect_app_items item on app.app_id = item.app_id and item.app_allow_type = 'hook' and app_item = '" . $hook_name . "' "
            . " join " . vmc::database()->prefix . "vmcconnect_hooks hook on hook.app_id = app.app_id "
            . " join " . vmc::database()->prefix . "vmcconnect_hook_items hook_item on hook_item.hook_id = hook.hook_id and hook_item.hook_item = '" . $hook_name . "' "
            . " where app_status = 1 "
            . " and app.app_hook_status = 1 "
            . " and hook.hook_status = 1 ";
        $rows = vmc::database()->exec($_sql);

        $res = ($rows && isset($rows['rs'])) ? $rows['rs'] : null;
        return $res;
    }
}
