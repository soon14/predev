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


class solr_ctl_admin_dashboard extends desktop_controller
{


    public function index()
    {
        $solr_stage = vmc::singleton('solr_stage');
        if(!$solr_stage->ping($ping_data)){
             $this->pagedata['ping_error'] = $ping_data;
        }else{
             $this->pagedata['ping_data'] = $ping_data;
        }
        $this->page('admin/dashboard.html');
    }
}
