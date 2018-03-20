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


class wechat_ctl_mobile_xcxpage extends mobile_controller {

    public $title = '小程序页面';

    public function __construct($app) {
        parent::__construct($app);
        $this->pagedata['base_url'] = vmc::base_url(true);
        $this->pagedata['res_url'] = $this->app->res_url;
    }

    public function index($page_sno) {
        $get_params = utils::_filter_input($_GET);
        $page_sno = $page_sno ? $page_sno : $get_params['s'];
        $mdl_xcxpage = app::get('wechat')->model('xcxpage');
        if (!$page_sno) {
            $xcxpage = $mdl_xcxpage->dump(array('is_homepage' => 'true'));
        } else {
            $xcxpage = $mdl_xcxpage->dump(array('sno' => $page_sno));
        }
        if (!$xcxpage) {
            $this->splash('error');
        } else {
            $this->pagedata = $xcxpage;
        }
        $this->page('mobile/default.html');
    }

    public function preview($page_id, $draft = true) {
        !$page_id && $page_id = $get_params ? $get_params : $get_params['page_id'];
        if(!$page_id) return;

        ($_params && isset($_params['draft'])) && $draft = $_params['draft'] ? true : false;
        $this->pagedata['is_draft'] = $draft;
        $this->pagedata['draft'] = $draft ? 'true' : 'false';

        $pageData = array();
        $page_id && $pageData = app::get('wechat')->model('xcxpage')->dump($page_id);

        vmc::singleton('wechat_xcxwidgets')->getData($pageData);
        //myfun::vard($pageData);

        $this->pagedata['data'] = $pageData;

        $this->pagedata['data_json'] = json_encode($this->pagedata['data']);
        $this->pagedata['gallery_remote'] = vmc::openapi_url('openapi.goods','gallery');

        $this->page('mobile/xcxpage/preview.html', true);
    }

}
