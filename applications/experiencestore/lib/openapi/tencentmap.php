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


class experiencestore_openapi_tencentmap extends base_openapi
{
    public function view($params)
    {
        $render = new base_render(app::get('experiencestore'));
        $render->_ignore_pre_display = true;
        $render->pagedata['params'] = $params;
        $render->display('common/tencentmap.html');
    }
}
