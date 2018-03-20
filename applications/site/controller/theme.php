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


class site_ctl_theme extends site_controller
{
    public function preview($theme,$theme_root_dir)
    {
        $theme_preview_cookie = utils::encrypt($theme);
        setcookie('CURRENT_THEME', $theme_preview_cookie);
        $_COOKIE['CURRENT_THEME'] = $theme_preview_cookie;

        if($theme_root_dir){
            $theme_root_dir_cookie = utils::encrypt($theme_root_dir);
            setcookie('THEME_DIR', $theme_root_dir_cookie);
            $_COOKIE['THEME_DIR'] = $theme_root_dir_cookie;
        }

        $this->redirect(array('app'=>'site','ctl'=>'index'));
    }
}
