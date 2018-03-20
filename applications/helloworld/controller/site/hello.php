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


class helloworld_ctl_site_hello extends site_frontpage
{
    public $title = '问候';
    public function __construct($app)
    {
        parent::__construct($app);
    }
    /**
     * 前台访问地址：
     * www.xxx.com/hello-1.html.
     */
    public function index($hello_id)
    {
        $mdl_hello = $this->app->model('hello');
        //$mdl_hello = app::get('helloworld')->model('hello');
        if (!$hello_id) {
            $this->splash('error', array('app' => 'helloworld', 'ctl' => 'site_hellolist', 'act' => 'index'), '发生错误');
        }
        $hello = $mdl_hello->dump($hello_id);
        $this->pagedata['hello'] = $hello;
        $this->set_tmpl('hello'); //设置模板页
        $this->page('site/detail.html');
    }
}
