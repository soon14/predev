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

class preselling_ctl_admin_member extends desktop_controller
{
    public function index()
    {
        $this->finder('preselling_mdl_participate_member', array(
            'title' => '预售用户参与列表',
            'use_buildin_filter' => true,
        ));
    }

}