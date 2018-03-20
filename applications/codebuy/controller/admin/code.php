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

class codebuy_ctl_admin_code extends desktop_controller
{
    public function index($activity_id)
    {
        $mdl_activity = $this->app->model('activity');
        $activity = $mdl_activity->getRow('*',array('id'=>$activity_id));
        $this->finder('codebuy_mdl_code', array(
            'title' => ('优购码列表') ,
            'use_buildin_export' => true,
            'use_buildin_recycle'=>false,
            'base_filter' => array('activity_id' => $activity_id),
            'finder_extra_view' => array(
                array(
                    'extra_pagedata'=> array('activity' => $activity),
                    'app' => 'codebuy',
                    'view' => '/admin/code_info.html'
                )
            ),
        ));
    }
}
