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
class commission_service_member_logout
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function logout(){
        setcookie('commission', 0, time() - 3600, '/' ,COOKIE_DOMAIN);
        setcookie('dp', 0, time() - 3600, '/' ,COOKIE_DOMAIN);
        setcookie('fmid', 0, time() - 3600, '/');
        $_COOKIE['commission'] = 0;
        $_COOKIE['dp'] = 0;
        $_COOKIE['commission'] = 0;
    }
}