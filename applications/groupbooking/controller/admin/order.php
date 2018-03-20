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

class groupbooking_ctl_admin_order extends desktop_controller
{
    public function index($status) {

        $base_filter = array('status' => $status,'main_id|in'=>'0');
        $this->finder('groupbooking_mdl_orders',array(
            'title' => '多人拼团',
            'use_buildin_recycle' => true,
            'use_buildin_filter' => true,
            'base_filter' => $base_filter,
        ));
    }

    public function detail($gb_id) {
        $this->pagedata['order'] = $this->app->model('orders')->getRow('*',array('gb_id'=>$gb_id));
        return $this->display('admin/order/detail.html');
    }

}