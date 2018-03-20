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


class toauth_ctl_admin_setting extends desktop_controller
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function index()
    {
        $mdl_pam = $this->app->model('pam');
        $payapp_list = $mdl_pam->getList('*');
        $this->pagedata['list'] = $payapp_list;
        $this->page('admin/pam/index.html');
    }

    public function setting($pam_class)
    {
        if (!$pam_class) {
            return false;
        } else {
            $pam_instance = new $pam_class();
            $setting = $pam_instance->setting();
        }
        if ($_POST['setting']) {
            $this->begin('index.php?app=toauth&ctl=admin_setting&act=index');
            foreach ($setting as $key => $value) {
                $conf[$key] = $_POST['setting'][$key];
            }
            $this->app->setConf($pam_class, serialize($conf));
            $this->end(true, '配置成功!');
        } else {
            if ($setting) {
                $render = $this->app->render();
                $render->pagedata['pam_name'] = $pam_instance->name;
                $render->pagedata['pam_version'] = $pam_instance->version;
                $render->pagedata['settings'] = $setting;
                $render->pagedata['conf'] = unserialize($this->app->getConf($pam_class));
                $render->pagedata['classname'] = $pam_class;
                $render->display('admin/pam/setting.html');
            }
        }
    }
}
