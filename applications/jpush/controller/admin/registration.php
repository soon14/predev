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


class jpush_ctl_admin_registration extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
        $this->app = $app;
    }

    public function index()
    {
        $this->finder('jpush_mdl_registration', array(
            'title' => ('客户端列表'),
            'use_buildin_filter' => true,
            'use_buildin_set_tag'=>true,
            'finder_extra_view' => array(array('app' => 'jpush','view' => '/admin/finder/registration.html')),
            // 'actions' => array(
            //     array(
            //         'label' => ('新建推送任务'),
            //         'icon' => 'fa-plus',
            //         'href' => 'index.php?app=jpush&ctl=admin_task&act=edit',
            //     ),
            // ),
        ));
    }

    public function quick_update_alias()
    {
        $this->begin('index.php?app=jpush&ctl=admin_registration&act=index');
        $params = $_POST;
        if (!$params['id'] ||!$params['alias']) {
            $this->end(false);
        }
        $mdl_stock = app::get('jpush')->model('registration');
        $this->end($mdl_stock->save($params));
    }
}
