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


class toauth_api extends base_openapi
{
    /**
     * 构造方法.
     *
     * @params string - app id
     */
    public function __construct($app)
    {
        $this->app = $app ? $app : app::get('toauth');
    }

    //信任登录回调入口
    public function callback($pam)
    {
        $params = vmc::singleton('base_component_request')->get_params(true);
        $pam_class = key($pam);
        $pam_method = current($pam);
        $pam_instance = new $pam_class();
        $pam_instance->$pam_method($params);
    }


}
