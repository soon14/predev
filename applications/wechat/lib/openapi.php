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


class wechat_openapi extends base_openapi {

    protected $app, $request, $params;

    public function __construct() {
        $this->app = app::get('wechat');
        $this->request = vmc::singleton('base_component_request');
        $this->params = $this->request->get_params(true);
    }

}
