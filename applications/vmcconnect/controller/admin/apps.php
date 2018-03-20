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

class vmcconnect_ctl_admin_apps extends desktop_controller {

    public $mod_apps;
    protected $_app_id;

    public function __construct($app) {
        parent::__construct($app);
        $this->mod_apps = $this->app->model('apps');
        $this->_app_id = $this->app->app_id;
    }

    public function index() {
        if ($this->has_permission('vmcconnect_apps_add')) {
            $custom_actions[] = array(
                'label' => ('添加服务'),
                'icon' => 'fa-plus',
                'href' => 'index.php?app=' . $this->_app_id . '&ctl=admin_apps&act=add',
            );
        }
        if ($this->has_permission('vmcconnect_apps_batch')) {
            $group[] = array(
                'label' => ('服务启用状态'),
                'data-submit' => 'index.php?app=' . $this->_app_id . '&ctl=admin_apps&act=set_status',
                'data-target' => '_ACTION_MODAL_',
            );

            $group[] = array(
                'label' => ('API启用状态'),
                'data-submit' => 'index.php?app=' . $this->_app_id . '&ctl=admin_apps&act=set_api_status',
                'data-target' => '_ACTION_MODAL_',
            );

            $group[] = array(
                'label' => ('HOOK启用状态'),
                'data-submit' => 'index.php?app=' . $this->_app_id . '&ctl=admin_apps&act=set_hook_status',
                'data-target' => '_ACTION_MODAL_',
            );

            $group[] = array(
                'label' => ('排序'),
                'data-submit' => 'index.php?app=' . $this->_app_id . '&ctl=admin_apps&act=set_order',
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
            $custom_actions['use_buildin_recycle'] = true;
        }

        $actions_base['title'] = ('服务管理');
        $actions_base['actions'] = $custom_actions;
        $this->finder('vmcconnect_mdl_apps', $actions_base);
    }

    public function add() {
        return $this->_app_form();
    }

    public function edit($app_id) {
        return $this->_app_form($app_id);
    }

    protected function _app_form($app_id = 0) {
        
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;

        if ($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/api/tpls.php')) {
            $_tpls = (include $_tmp_file);
        }else{
            $_tpls = is_file($_tpl_file = $this->app->app_dir . '/vars/api/tpls.php') ? (include $_tpl_file) : null;
        }
        
        $this->pagedata['api_tpls'] = $_tpls;

        $set_app = array();
        $set_app['app_secret'] = $this->mod_apps->random(32);
        $set_app['app_com_tpl'] = 'def';
        $set_app['app_order'] = 2000;
        $set_app['app_status'] = 1;
        $set_app['app_api_status'] = 1;
        $set_app['app_hook_status'] = 1;

        if ($app_id) {
            $set_app = $this->mod_apps->getRow('*', array(
                'app_id' => $app_id,
            ));
        }

        $this->pagedata['set_app'] = $set_app;
        $this->display('admin/apps/edit.html');
    }

    public function save() {
        $this->begin('index?app=' . $this->_app_id . '&ctl=admin_apps&act=index');

        $app_sets = ($_POST && isset($_POST['app_sets']) && is_array($_POST['app_sets'])) ? $_POST['app_sets'] : null;
        !$app_sets && $this->end(false, '保存失败!');

        $app_id = isset($_POST['app_id']) ? (int) $_POST['app_id'] : 0;

        $app_name = isset($app_sets['app_name']) ? trim($app_sets['app_name']) : null;
        !strlen($app_name) && $this->end(false, '请填写服务名称!');

        $app_secret = isset($app_sets['app_secret']) ? trim($app_sets['app_secret']) : null;
        !strlen($app_secret) && $this->end(false, '请填写服务加密串!');

        !isset($app_sets['app_order']) && $app_sets['app_order'] = 2000;
        $app_id && $app_sets['app_id'] = $app_id;
        $result = $this->mod_apps->save($app_sets);
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

    public function set_status() {
        $this->pagedata['app_status'] = 1;
        return $this->__sets('status');
    }

    public function set_api_status() {
        $this->pagedata['app_api_status'] = 1;
        return $this->__sets('api_status');
    }

    public function set_hook_status() {
        $this->pagedata['app_hook_status'] = 1;
        return $this->__sets('hook_status');
    }

    public function set_order() {
        $this->pagedata['app_order'] = 2000;
        return $this->__sets('order');
    }

    private function __sets($type) {
        $type = trim($type);
        if (!strlen($type) || !in_array($type, array('status', 'api_status', 'hook_status', 'order'))) {
            echo('<div class="alert alert-warning">操作有误!</div>');
            exit;
        }

        $filter = $_POST;
        if (empty($filter)) {
            echo('<div class="alert alert-warning">请选择要操作的数据!</div>');
            exit;
        }

        $this->pagedata['filter'] = htmlspecialchars(serialize($filter));

        $this->display('admin/apps/sets_' . $type . '.html');
    }

    public function save_status() {
        return $this->__saves('status');
    }

    public function save_api_status() {
        return $this->__saves('api_status');
    }

    public function save_hook_status() {
        return $this->__saves('hook_status');
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
        if (!strlen($type) || !in_array($type, array('status', 'api_status', 'hook_status', 'order')) || !$filter || !$set) {
            $this->end(false, '操作错误');
        }

        switch ($type) {
            case 'status':
            case 'api_status':
            case 'hook_status':
            case 'order':
                if ($this->mod_apps->update($set, $filter)) {
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

    public function logs($app_id, $log_type) {
        if (!$app_id) return false;
        switch ($log_type) {
            case 'setting':
                $this->finder(
                        'operatorlog_mdl_normallogs', array(
                    'title' => $this->app->_('操作日志'),
                    'allow_detail_popup' => true,
                    'use_buildin_recycle' => false,
                    'use_buildin_selectrow' => false,
                    'base_filter' => array('module|in' => array(
                            'vmcconnect|apps|' . $app_id . '|create',
                            'vmcconnect|apps|' . $app_id . '|update',
                        )),
                        )
                );
                break;
            case 'api':
                $this->finder(
                        'operatorlog_mdl_normallogs', array(
                    'title' => $this->app->_('操作日志'),
                    'allow_detail_popup' => true,
                    'use_buildin_recycle' => false,
                    'use_buildin_selectrow' => false,
                    'base_filter' => array('module' => 'vmcconnect|apps|' . $app_id . '|api|items|allow'),
                        )
                );
                break;
            case 'hook':
                $this->finder(
                        'operatorlog_mdl_normallogs', array(
                    'title' => $this->app->_('操作日志'),
                    'allow_detail_popup' => true,
                    'use_buildin_recycle' => false,
                    'use_buildin_selectrow' => false,
                    'base_filter' => array('module' => 'vmcconnect|apps|' . $app_id . '|hooks|items|allow'),
                        )
                );
                break;
        }
    }

    function apilog($app_id) {

        $this->finder(
                'vmcconnect_mdl_apilogs', array(
            'title' => $this->app->_('API日志'),
            'allow_detail_popup' => true,
            'use_buildin_filter' => false,
            'use_buildin_recycle' => false,
            'use_buildin_selectrow' => false,
            'base_filter' => array('app_key' => $app_id),
                )
        );
    }
    
    function queues($app_id) {

        $this->finder(
                'vmcconnect_mdl_hooktask_items', array(
            'title' => $this->app->_('HOOK执行日志'),
            'allow_detail_popup' => true,
            'use_buildin_filter' => false,
            'use_buildin_recycle' => false,
            'use_buildin_selectrow' => false,
            'base_filter' => array('app_key' => $app_id),
                )
        );
    }

}
