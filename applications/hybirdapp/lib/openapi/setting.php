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


class hybirdapp_openapi_setting extends base_openapi
{
    private $req_params = array();

    public function __construct($app)
    {
        $this->app = $app;
        $this->req_params = vmc::singleton('base_component_request')->get_params(true);
    }

    public function ios()
    {
        $setting = $this->get_setting('iOS');
        $this->success($setting);
    }

    public function android()
    {
        $setting = $this->get_setting('Android');
        $this->success($setting);
    }

    private function get_setting($type = 'ios')
    {
        include($this->app->app_dir.'/setting.php');
        $_return = array();
        foreach ($setting as $key => $value) {
            if(strpos($key,$type.'_') === 0){
                $_return[$key] = $this->app->getConf($key);
            }
        }
        return $_return;
    }
}
