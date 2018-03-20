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


class mobile_ctl_theme extends mobile_controller
{
    public function preview($theme,$theme_root_dir_cookie)
    {
        $theme_preview_cookie = utils::encrypt($theme);
        setcookie('CURRENT_THEME_M', $theme_preview_cookie);
        $_COOKIE['CURRENT_THEME_M'] = $theme_preview_cookie;
        
        if($theme_root_dir){
            $theme_root_dir_cookie = utils::encrypt($theme_root_dir);
            setcookie('THEME_M_DIR', $theme_root_dir_cookie);
            $_COOKIE['THEME_M_DIR'] = $theme_root_dir_cookie;
        }

        $this->redirect(array('app'=>'mobile','ctl'=>'index'));
    }
}
