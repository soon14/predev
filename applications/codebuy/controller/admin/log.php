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

class codebuy_ctl_admin_log extends desktop_controller
{
    public function index()
    {
        $this->finder('codebuy_mdl_log', array(
            'title' => ('优购码使用记录') ,
            'use_buildin_export' => true,
            'use_buildin_recycle'=>false,
        ));
    }
}
