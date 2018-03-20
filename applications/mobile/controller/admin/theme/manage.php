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


class mobile_ctl_admin_theme_manage extends desktop_controller
{
    //列表
    public function index()
    {
        vmc::singleton('mobile_theme_install')->check();
        $mdl_themes = $this->app->model('themes');
        $this->pagedata['themes'] = $mdl_themes->getList('*');
        foreach ($this->pagedata['themes'] as &$theme) {
            $theme['cache_version'] =  $this->app->getConf('theme_cache_version.'.$theme['theme_dir']);
        }
        if(app::get('widgets')->status() == 'active'){
            $this->pagedata['widgets_active'] = 'true';
        }
        $this->page('/admin/theme/index.html');

    }//End Function



    public function detail()
    {

    }


    public function set_default($theme)
    {
        $this->begin('index.php?app=mobile&ctl=admin_theme_manage');
        if ($theme) {
            if (vmc::singleton('mobile_theme_base')->set_default($theme)) {
                $this->end(true, '设置成功');
            } else {
                $this->end(false, '设置失败');
            }
        }else{
            $this->end(false);
        }
    }//End Function


    public function delete()
    {
        $this->begin();
        $get = $this->_request->get_get();
        if (app::get('mobile')->model('themes')->delete_file(array('theme' => $get['theme']))) {
            $this->end(true, '删除成功', 'index.php?app=site&ctl=admin_theme_manage&act=index');
        } else {
            $this->end(false, '删除失败');
        }
    }//End Function



    public function cache_version($theme)
    {
        $this->begin();
        $this->end(vmc::singleton('mobile_theme_install')->monitor_change($theme));
    }//End Function

}//End Class
