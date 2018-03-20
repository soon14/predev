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

class groupbooking_ctl_admin_member extends desktop_controller
{
    public function index()
    {
        $this->finder('groupbooking_mdl_participate_member', array(
            'title' => '多人拼团用户参与列表',
            'use_buildin_filter' => true,
        ));
    }

}