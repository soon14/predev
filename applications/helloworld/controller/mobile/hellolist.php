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


class helloworld_ctl_mobile_hellolist extends mobile_frontpage
{
    public $title = '问候列表';
    public function __construct($app)
    {
        parent::__construct($app);
    }
    /**
     * 前台访问地址：
     * www.xxx.com/hlist.html
     * www.xxx.com/hlist-1.html.
     */
    public function index($page = 1)
    {
        $limit = 20;
        $mdl_hello = $this->app->model('hello');
        //$mdl_hello = app::get('helloworld')->model('hello');
        $filter = array();
        $hello_list = $mdl_hello->getList('*', $filter, ($page - 1) * $limit, $limit);

        $hello_count = $mdl_hello = $mdl_order->count($filter);

        $this->pagedata['hellolist'] = $hello_list;
        $this->pagedata['pager'] = array(
            'total' => ceil($hello_count / $limit) ,
            'current' => $page,
            'link' => array(
                'app' => 'helloworld',
                'ctl' => 'site_hellolist',
                'act' => 'index',
                'args' => array(
                    ($token = time()),
                ) ,
            ) ,
            'token' => $token,
        );
        $this->set_tmpl('hellolist'); //设置模板页
        $this->page('site/list.html');
    }
}
