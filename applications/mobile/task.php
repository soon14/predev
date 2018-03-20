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


class mobile_task
{
    public function post_install()
    {
        vmc::singleton('base_initial', 'mobile')->init();
        pam_account::register_account_type('mobile', 'member', '前台会员系统');
        $themes = vmc::singleton('mobile_theme_install')->check();
        //更新mobile.xml
        $rows = app::get('base')->model('apps')->getList('app_id', array('installed' => 1));
        foreach ($rows as $r) {
            if ($r['app_id'] == 'base' || $r['app_id'] == 'mobile') {
                continue;
            }
            $args[] = $r['app_id'];
        }
        foreach ((array) $args as $app) {
            $this->xml_update($app);
        }
    }//End Function

    public function post_update($appinfo)
    {


    }

    /**
     * xml文件的更新操作.
     *
     * @param object $app app对象实例
     */
    private function xml_update($app)
    {
        if (!$app) {
            return;
        }
        $detector = vmc::singleton('mobile_application_module');
        foreach ($detector->detect($app) as $name => $item) {
            $item->install();
        }
    }

    public function post_uninstall()
    {
        app::get('mobile')->setConf('select_terminator', 'false');
    }
}//End Class
