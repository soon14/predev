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

class vmcconnect_ctl_admin_hooks extends desktop_controller {

    public $mod_hooks, $mod_apps;
    protected $_app_id;

    public function __construct($app) {
        parent::__construct($app);
        $this->mod_hooks = $this->app->model('hooks');
        $this->mod_apps = $this->app->model('apps');
        $this->_app_id = $this->app->app_id;
    }

    public function index($app_id) {
        $app_id = (int) $app_id;
        $app_info = $this->mod_apps->dump($app_id);
        $this->pagedata['app_info'] = $app_info;
        $this->pagedata['app_id'] = $app_id;

        if ($this->has_permission('vmcconnect_apps_add')) {
            $custom_actions[] = array(
                'label' => ('添加HOOK'),
                'icon' => 'fa-plus',
                'href' => 'index.php?app=' . $this->_app_id . '&ctl=admin_hooks&act=add&p[0]=' . $app_id,
            );
        }
        if ($this->has_permission('vmcconnect_apps_batch')) {
            $group[] = array(
                'label' => ('服务启用状态'),
                'data-submit' => 'index.php?app=' . $this->_app_id . '&ctl=admin_hooks&act=set_status',
                'data-target' => '_ACTION_MODAL_',
            );
            $group[] = array(
                'label' => ('排序'),
                'data-submit' => 'index.php?app=' . $this->_app_id . '&ctl=admin_hooks&act=set_order',
                'data-target' => '_ACTION_MODAL_',
            );
        }
        if ($group) {
            $custom_actions[] = array(
                'label' => ('批量操作'),
                'group' => $group,
            );
        }

        if ($this->has_permission('vmcconnect_apps_del')) {
            $use_buildin_set_tag = true;
        }

        $actions_base['title'] = ('应用HOOK管理');
        $actions_base['actions'] = $custom_actions;
        //$actions_base['use_buildin_set_tag'] = $use_buildin_set_tag;
        //$actions_base['use_buildin_export'] = $use_buildin_export;
        //$actions_base['use_buildin_filter'] = true;
        $this->finder('vmcconnect_mdl_hooks', $actions_base);
    }

    public function add($app_id) {
        return $this->_hook_form($app_id);
    }

    public function edit($app_id = 0, $hook_id = 0) {
        return $this->_hook_form($app_id, $hook_id);
    }

    protected function _hook_form($app_id = 0, $hook_id = 0) {

        $set_hook = array();
        $set_hook['hook_status'] = 1;
        $set_hook['hook_order'] = 2000;
        $set_hook['hook_msg_tpl'] = 'def';

        $app_id = (int) $app_id;
        $hook_id = (int) $hook_id;

        $hook_id && $set_hook = $this->mod_hooks->dump($hook_id);
        !$app_id && $set_hook && $set_hook['app_id'] && $app_id = $set_hook['app_id'];

        $app_id && !$set_hook['app_id'] && $set_hook['app_id'] = $app_id;

        $app_info = $this->mod_apps->dump($app_id);
        $this->pagedata['app_info'] = $app_info;
        $this->pagedata['app_id'] = $app_id;
        $this->pagedata['hook_id'] = $hook_id;
        
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;

        if ($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/hook/msgs.php')) {
            $_msgs = (include $_tmp_file);
        }else{
            $_msgs = is_file($_tpl_file = $this->app->app_dir . '/vars/hook/msgs.php') ? (include $_tpl_file) : null;
        }
        
        $this->pagedata['hook_msgs'] = $_msgs;

        $_obj_map = vmc::singleton('vmcconnect_object_hook_map');
        $_obj_map->setapp($this->app);
        $app_allow_hooks = $_obj_map->app_allow_hooks($app_id);
        $this->pagedata['app_allow_hooks'] = $app_allow_hooks;

        $hook_id && $hook_allow_items = $this->app->model('hook_items')->get_items($hook_id, $app_id);
        $this->pagedata['hook_allow_items'] = $hook_allow_items;
        
        $this->pagedata['set_hook'] = $set_hook;
        //将VMC基础设置中填写的手机号码填入
        $conf_data = $this->app->getConf('vmcconnect-warning-conf');
        $hook_alert_phone = $conf_data['hook_alert_phone'];
        $this->pagedata['hook_alert_phone'] = $hook_alert_phone;
        $this->display('admin/hooks/edit.html');
    }

    public function save($app_id = 0, $hook_id = 0) {
        $app_id = (int) $app_id;
        $hook_id = (int) $hook_id;
        (!$app_id && $_POST && isset($_POST['app_id'])) && $app_id = (int) $_POST['app_id'];
        (!$hook_id && $_POST && isset($_POST['hook_id'])) && $hook_id = (int) $_POST['hook_id'];

        $this->begin('index?app=' . $this->_app_id . '&ctl=admin_hooks&act=index&p[0]=' . $app_id);

        $hook_sets = ($_POST && isset($_POST['hook_sets']) && is_array($_POST['hook_sets'])) ? $_POST['hook_sets'] : null;
        !$hook_sets && $this->end(false, '保存失败!');

        $hook_sets['hook_name'] = isset($hook_sets['hook_name']) ? trim($hook_sets['hook_name']) : null;
        !strlen($hook_sets['hook_name']) && $this->end(false, '请填写HOOK服务名称!');

        $hook_sets['hook_url'] = isset($hook_sets['hook_url']) ? trim($hook_sets['hook_url']) : null;
        !strlen($hook_sets['hook_url']) && $this->end(false, '请填写HOOK服务URL!');

        //保存hook_alert_phone到数组中
        $hook_sets['hook_alert_phone'] = isset($hook_sets['hook_phone']) ? trim($hook_sets['hook_phone']) : null;
        !strlen($hook_sets['hook_alert_phone']) && $this->end(false, '请填写HOOK预警号码!');

        $hook_sets['hook_msg_tpl'] = isset($hook_sets['hook_msg_tpl']) ? trim($hook_sets['hook_msg_tpl']) : 'def';
        $hook_sets['hook_order'] = (!isset($hook_sets['hook_order']) || !$hook_sets['hook_order']) ? 2000 : $hook_sets['hook_order'];

        $hook_url_exists = $this->mod_hooks->urlExists($app_id, $hook_sets['hook_url'], $hook_id);
        $hook_url_exists && $this->end(false, '当前HOOK服务URL已存在!');

        $hook_sets['hook_addon'] = isset($hook_sets['hook_addon']) ? trim($hook_sets['hook_addon']) : '';

        $hook_sets['app_id'] = $app_id;
        $hook_id && $hook_sets['hook_id'] = $hook_id;
        $result = $this->mod_hooks->save($hook_sets);

        if ($result) {
            $this->end(true, '保存成功!');
        } else {
            $this->end(false, '保存失败!');
        }
    }

    public function set_status() {
        $this->pagedata['hook_status'] = 1;
        return $this->__sets('status');
    }

    public function set_order() {
        $this->pagedata['hook_order'] = 2000;
        return $this->__sets('order');
    }

    private function __sets($type) {
        $type = trim($type);
        if (!strlen($type) || !in_array($type, array('status', 'order'))) {
            echo('<div class="alert alert-warning">操作有误!</div>');
            exit;
        }

        $filter = $_POST;
        if (empty($filter)) {
            echo('<div class="alert alert-warning">请选择要操作的数据!</div>');
            exit;
        }

        $this->pagedata['filter'] = htmlspecialchars(serialize($filter));

        $this->display('admin/hooks/sets_' . $type . '.html');
    }

    public function save_status() {
        return $this->__saves('status');
    }

    public function save_order() {
        return $this->__saves('order');
    }

    private function __saves($type) {
        $this->begin();
        $type = trim($type);
        $params = $_POST;
        $filter = unserialize(trim($params['filter']));
        $set = $params['set'];
        if (!strlen($type) || !in_array($type, array('status', 'order')) || !$filter || !$set) {
            $this->end(false, '操作错误');
        }

        switch ($type) {
            case 'status':
            case 'order':
                if ($this->mod_hooks->update($set, $filter)) {
                    $this->end(true, '保存成功');
                } else {
                    $this->end(false, '保存失败');
                }
                break;
            default :
                $this->end(true, '操作错误');
                break;
        }
    }

    public function logs($app_id, $hook_id, $log_type) {
        $hook = $this->mod_hooks->dump($hook_id);
        $app_id = $hook['app_id'];
        if (!$app_id || !$hook_id) return false;
        // 'vmcconnect|hooks|' . $_app_id . '-' . $newData['hook_id'] . '|create
        // 'vmcconnect|hooks|' . $_app_id . '-' . $newData['hook_id'] . '|update
        switch ($log_type) {
            case 'setting':
                $this->finder(
                        'operatorlog_mdl_normallogs', array(
                    'title' => $this->app->_('操作日志'),
                    'allow_detail_popup' => true,
                    'use_buildin_recycle' => false,
                    'use_buildin_selectrow' => false,
                    'base_filter' => array('module|in' => array(
                            'vmcconnect|hooks|' . $app_id . '-' . $hook_id . '|create',
                            'vmcconnect|hooks|' . $app_id . '-' . $hook_id . '|update',
                            'vmcconnect|hooks|' . $app_id . '-' . $hook_id . '|items|allow',
                        )),
                        )
                );
                break;
        }
    }

    function queues($app_id, $hook_id) {
        $this->finder(
                'vmcconnect_mdl_hooktask_items', array(
            'title' => $this->app->_('HOOK执行日志'),
            'allow_detail_popup' => true,
            'use_buildin_filter' => false,
            'use_buildin_recycle' => false,
            'use_buildin_selectrow' => false,
            'base_filter' => array('hook_key' => $hook_id),
                )
        );
    }

}
