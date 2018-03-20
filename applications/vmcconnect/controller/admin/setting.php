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

class vmcconnect_ctl_admin_setting extends desktop_controller {

    public function index() {

        //$_obj_map = vmc::singleton('vmcconnect_object_api_map');
        //$_obj_map->setapp($this->app);
        //$apis = $_obj_map->all_apis();

        $get_api_conf = $this->app->getConf('vmcconnect-api-conf');
        $api_conf = $get_api_conf ? unserialize($get_api_conf) : null;

        $get_hook_conf = $this->app->getConf('vmcconnect-hook-conf');
        $hook_conf = $get_hook_conf ? unserialize($get_hook_conf) : null;

        $this->pagedata['api_conf'] = $api_conf;
        $this->pagedata['hook_conf'] = $hook_conf;

        $this->page('admin/setting.html');
    }

    public function setting($type) {
        $type = trim($type);
        switch ($type) {
            default :
            case 'api':
                return $this->_setting_api();
                break;
            case 'hook':
                return $this->_setting_hook();
                break;
                //VMCC基本配置页面增加的警报设置功能
            case 'warning':
                return $this->_setting_warning();
                break;
        }
    }

    protected function _setting_api() {
        
        $_tpls = false;
        
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;
        
        if($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/api/tpls.php')){
            $_tpls = (include $_tmp_file);
        }else{
            $_tpls = is_file($_tpl_file = $this->app->app_dir . '/vars/api/tpls.php') ? (include $_tpl_file) : null;
        }
        
        $this->pagedata['api_tpls'] = $_tpls;

        $_obj_map = vmc::singleton('vmcconnect_object_api_map');
        $_obj_map->setapp($this->app);
        $apis = $_obj_map->all_apis();

        $get_conf = $this->app->getConf('vmcconnect-api-conf');
        $api_conf = $get_conf ? unserialize($get_conf) : null;
        $_conf_is_empty = false;
        if (!$api_conf) {
            $_conf_is_empty = true;
            $api_conf['api_enable'] = 'true';
            $_tmp_items = $_obj_map->all_api_items();
            $api_conf['api_items'] = ($_tmp_items ? array_keys($_tmp_items) : array());
        }

        if ($_POST) {

            $_api_enable = (isset($_POST['api_enable'])) ? $_POST['api_enable'] : false;
            $_api_items = ($_api_enable == 'true' && isset($_POST['api_items'])) ? ($_POST['api_items'] ? array_values($_POST['api_items']) : array()) : array();
            $_api_def_tpl = (isset($_POST['api_def_tpl'])) ? $_POST['api_def_tpl'] : 'def';

            $this->begin('index.php?app=vmcconnect&ctl=admin_setting&act=index');
            $conf['api_enable'] = $_api_enable;
            $conf['api_items'] = $_api_items;
            $conf['api_def_tpl'] = $_api_def_tpl;
            $this->app->setConf('vmcconnect-api-conf', serialize($conf));

            foreach (vmc::servicelist('vmcconnect.log.setting') as $object) {
                if (method_exists($object, 'sys_api_conf')) {
                    $object->sys_api_conf($conf, ($_conf_is_empty ? array() : $api_conf));
                }
            }

            $this->end(true, '配置成功!');
        }

        (!isset($api_conf['api_def_tpl']) || !$api_conf['api_def_tpl']) && $api_conf['api_def_tpl'] = 'def';

        $this->pagedata['api_conf'] = $api_conf;
        $this->pagedata['all_apis'] = $apis;

        $this->page('admin/setting-api.html');
    }

    protected function _setting_hook() {
        
        $_msgs = false;
        
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;
        
        if($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/hook/msgs.php')){
            $_msgs = (include $_tmp_file);
        }else{
            $_msgs = is_file($_msg_file = $this->app->app_dir . '/vars/hook/msgs.php') ? (include $_msg_file) : null;
        }
        
        $this->pagedata['hook_msgs'] = $_msgs;

        $_obj_map = vmc::singleton('vmcconnect_object_hook_map');
        $_obj_map->setapp($this->app);
        $hooks = $_obj_map->all_hooks();

        $get_conf = $this->app->getConf('vmcconnect-hook-conf');
        $hook_conf = $get_conf ? unserialize($get_conf) : null;
        $_conf_is_empty = false;
        if (!$hook_conf) {
            $_conf_is_empty = true;
            $hook_conf['hook_enable'] = 'true';
            $_tmp_items = $_obj_map->all_hook_items();
            $hook_conf['hook_items'] = ($_tmp_items ? array_keys($_tmp_items) : array());
        }

        if ($_POST) {

            $_hook_enable = (isset($_POST['hook_enable'])) ? $_POST['hook_enable'] : false;
            $_hook_items = ($_hook_enable == 'true' && isset($_POST['hook_items'])) ? ($_POST['hook_items'] ? array_values($_POST['hook_items']) : array()) : array();
            $_hook_def_msg = (isset($_POST['hook_def_msg'])) ? $_POST['hook_def_msg'] : 'def';

            $this->begin('index.php?app=vmcconnect&ctl=admin_setting&act=index');
            $conf['hook_enable'] = $_hook_enable;
            $conf['hook_items'] = $_hook_items;
            $conf['hook_def_msg'] = $_hook_def_msg;
            $this->app->setConf('vmcconnect-hook-conf', serialize($conf));

            foreach (vmc::servicelist('vmcconnect.log.setting') as $object) {
                if (method_exists($object, 'sys_hook_conf')) {
                    $object->sys_hook_conf($conf, ($_conf_is_empty ? array() : $hook_conf));
                }
            }

            $this->end(true, '配置成功!');
        }


        (!isset($hook_conf['hook_def_msg']) || !$hook_conf['hook_def_msg']) && $hook_conf['hook_def_msg'] = 'def';

        $this->pagedata['hook_conf'] = $hook_conf;
        $this->pagedata['all_hooks'] = $hooks;

        $this->page('admin/setting-hook.html');
    }

    //警报设置页面
    protected function _setting_warning() {
        
        $_msgs = false;
        
        $ext_app_dir = EXTENDS_DIR ? (EXTENDS_DIR . DIRECTORY_SEPARATOR . $this->app->app_id) : false;
        
        if($ext_app_dir && is_file($_tmp_file = $ext_app_dir . '/vars/hook/msgs.php')){
            $_msgs = (include $_tmp_file);
        }else{
            $_msgs = is_file($_msg_file = $this->app->app_dir . '/vars/hook/msgs.php') ? (include $_msg_file) : null;
        }
        
        $this->pagedata['warning_msgs'] = $_msgs;    //模板默认/京东

        //olddata
        $get_conf = $this->app->getConf('vmcconnect-warning-conf');
        $warning_conf = $get_conf ? $get_conf : null;

        if ($_POST) {
            //是否启用
            $_warning_enable = (isset($_POST['warning_enable'])) ? $_POST['warning_enable'] : false;
            //api超限警报次数
            $_api_alert = (isset($_POST['api_alert']) && is_numeric($_POST['api_alert'])) ? (int)$_POST['api_alert'] : '';
            //警报通知短信
            $_api_alert_phone = (isset($_POST['api_alert_phone']) && is_numeric($_POST['api_alert_phone'])) ? $_POST['api_alert_phone'] : '';
            //hook超限警报次数
            $_hook_alert = (isset($_POST['hook_alert']) && is_numeric($_POST['hook_alert'])) ? (int)$_POST['hook_alert'] : '';
            //警报通知短信
            $_hook_alert_phone = (isset($_POST['hook_alert_phone']) && is_numeric($_POST['hook_alert_phone'])) ? $_POST['hook_alert_phone'] : '';

            $this->begin('index.php?app=vmcconnect&ctl=admin_setting&act=index');
            $conf['warning_enable'] = $_warning_enable;
            $conf['api_alert'] = $_api_alert;
            $conf['api_alert_phone'] = $_api_alert_phone;
            $conf['hook_alert'] = $_hook_alert;
            $conf['hook_alert_phone'] = $_hook_alert_phone;

            $this->app->setConf('vmcconnect-warning-conf', $conf);
            foreach (vmc::servicelist('vmcconnect.log.setting') as $object) {
                if (method_exists($object, 'sys_warning_conf')) {
                    $object->sys_warning_conf($conf, ($_conf_is_empty ? array() : $warning_conf));
                }
            }

            $this->end(true, '配置成功!');
        }

        $this->pagedata['warning_conf'] = $warning_conf;

        $this->page('admin/setting-warning.html');
    }

    public function logs($type) {
        $this->finder(
                'operatorlog_mdl_normallogs', array(
            'title' => $this->app->_('操作日志'),
            'allow_detail_popup' => true,
            'use_buildin_recycle' => false,
            'use_buildin_selectrow' => false,
            'base_filter' => array('module' => 'vmcconnect|setting|' . $type),
                )
        );
    }

}
