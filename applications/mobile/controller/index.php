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


/*触屏端首页入口*/
class mobile_ctl_index extends mobile_controller
{
    public $title = '首页';

    public function index()
    {
        if (vmc::singleton('mobile_theme_base')->theme_exists()) {
            $this->set_tmpl('index');
            $this->page('index.html');
        } else {
            $this->display('no_theme.html');
        }
    }
}
