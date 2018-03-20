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

class vmcconnect_ctl_admin_hook_queues extends desktop_controller {
    
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
                'label' => ('启用'),
                'data-submit' => 'index.php?app=' . $this->_app_id . '&ctl=admin_hooks&act=set_status&status=1',
                'data-target' => '_ACTION_MODAL_',
            );
            $group[] = array(
                'label' => ('禁用'),
                'data-submit-result' => 'index.php?app=' . $this->_app_id . '&ctl=admin_hooks&act=set_status&status=0',
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

        $hook_sets['hook_msg_tpl'] = isset($app_sets['hook_msg_tpl']) ? trim($app_sets['hook_msg_tpl']) : 'def';
        $hook_sets['hook_order'] = (!isset($hook_sets['hook_order']) || !$hook_sets['hook_order']) ? 2000 : $hook_sets['hook_order'];

        $hook_url_exists = $this->mod_hooks->urlExists($app_id, $hook_sets['hook_url'], $hook_id);
        $hook_url_exists && $this->end(false, '当前HOOK服务URL已存在!');
        
        $hook_sets['app_id'] = $app_id;
        $hook_id && $hook_sets['hook_id'] = $hook_id;
        $result = $this->mod_hooks->save($hook_sets);
        
        if ($result) {
            $this->end(true, '保存成功!');
        } else {
            $this->end(false, '保存失败!');
        }
    }

    public function apis($app_id) {

        $mod_app_items = $this->app->model('app_items');
        $app_info = $this->mod_apps->dump($app_id);
        $this->pagedata['app_info'] = $app_info;

        $_obj_map = vmc::singleton('vmcconnect_object_api_map');
        $_obj_map->setapp($this->app);
        $all_allow_apis = $_obj_map->sys_allow_apis();
        $all_allow_api_items = $_obj_map->sys_allow_apis();

        $this->pagedata['allow_apis'] = $all_allow_apis;


        $app_get_items = $mod_app_items->get_allow_api_items($app_id);
        $this->pagedata['app_get_items'] = $app_get_items;

        if ($_POST) {
            $set_app_items = isset($_POST['set_app_items']) ? $_POST['set_app_items'] : null;
            $this->begin('index.php?app=' . $this->_app_id . '&ctl=admin_apps&act=api&p[0]=' . $set_app_items['app_id']);
            $_set_app_id = (int) $set_app_items['app_id'];
            !$_set_app_id && $this->end(false, '参数错误，编辑失败');
            $_app_item = isset($set_app_items['app_item']) ? $set_app_items['app_item'] : array();

            if ($mod_app_items->set_api_items($_set_app_id, $_app_item)) {
                $this->end(true, '编辑成功');
            } else {
                $this->end(false, '编辑失败');
            }
        }

        $this->display('admin/apps/apis.html');
    }

    public function hooks($app_id) {
        $mod_app_items = $this->app->model('app_items');
        $app_info = $this->mod_apps->dump($app_id);
        $this->pagedata['app_info'] = $app_info;

        $_obj_map = vmc::singleton('vmcconnect_object_hook_map');
        $_obj_map->setapp($this->app);
        $all_allow_hooks = $_obj_map->sys_allow_hooks();


        $this->pagedata['allow_hooks'] = $all_allow_hooks;


        $app_get_items = $mod_app_items->get_allow_hook_items($app_id);
        $this->pagedata['app_get_items'] = $app_get_items;

        if ($_POST) {
            $set_app_items = isset($_POST['set_app_items']) ? $_POST['set_app_items'] : null;
            $this->begin('index.php?app=' . $this->_app_id . '&ctl=admin_apps&act=api&p[0]=' . $set_app_items['app_id']);
            $_set_app_id = (int) $set_app_items['app_id'];
            !$_set_app_id && $this->end(false, '参数错误，编辑失败');
            $_app_item = isset($set_app_items['app_item']) ? $set_app_items['app_item'] : array();

            if ($mod_app_items->set_hook_items($_set_app_id, $_app_item)) {
                $this->end(true, '编辑成功');
            } else {
                $this->end(false, '编辑失败');
            }
        }

        $this->display('admin/apps/hooks.html');
    }

}
