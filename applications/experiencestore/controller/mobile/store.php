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


class experiencestore_ctl_mobile_store extends mobile_controller
{
    public $title = '地点';
    public function __construct($app)
    {
        parent::__construct($app);
    }
    public function index()
    {
        $this->set_tmpl('store_index');
        $this->page('mobile/store/index.html');
    }
    public function storelist()
    {
        $this->title = '全部地点';
        $mdl_store = $this->app->model('store');
        $store_list = $mdl_store->getList('*', array('disabled' => 'false'));
        $mdl_subject = $this->app->model('activity_subject');
        $mdl_schedule = $this->app->model('activity_schedule');
        foreach ($store_list as $key => $value) {
            $subject_id = $mdl_schedule->getColumn('subject_id', array('store_id' => $value['id'], 'end_time|bthan' => time(), 'is_pub' => 'true'));
            $store_list[$key]['subject'] = $mdl_subject->getList('*', array('id' => $subject_id));
        }
        $this->pagedata['store_list'] = $store_list;
        $this->set_tmpl('store_list');
        $this->page('mobile/store/list.html');
    }
    public function desc($store_id)
    {
        $mdl_store = $this->app->model('store');
        $store = $mdl_store->getRow('`id`,`name`,`desc`', array('id' => $store_id, 'disabled' => 'false'));
        $this->pagedata['store'] = $store;
        $this->page('mobile/store/desc.html');
    }
    public function detail($store_id)
    {
        $mdl_store = $this->app->model('store');
        $store = $mdl_store->dump($store_id, '*', 'default');
        if ($store['disabled'] == 'true') {
            $this->splash('error');
        }
        if (base_component_request::is_wxapp()) {
            $store['desc'] = $this->html_filter($store['desc']);
        }
        $this->title = $store['name'];
        $this->pagedata['store'] = $store;
        $store_gallery = $store['images'];
        foreach ($store_gallery as $key => $value) {
            if ($value['image_id'] == $store['gallery_default_image_id']) {
                unset($store_gallery[$key]);
            }
        }
        $store_gallery = array_reverse($store_gallery);
        array_push($store_gallery, array('image_id' => $store['gallery_default_image_id']));
        $store_gallery = array_reverse($store_gallery);
        $mdl_subject = $this->app->model('activity_subject');
        $mdl_schedule = $this->app->model('activity_schedule');
        $subject_id = $mdl_schedule->getColumn('subject_id', array('store_id' => $store['id'], 'end_time|bthan' => time(), 'is_pub' => 'true'));
        $this->pagedata['subject'] = $mdl_subject->getList('*', array('id' => $subject_id));

        $this->pagedata['store_gallery'] = $store_gallery;
        $this->set_tmpl('store_detail');
        $this->page('mobile/store/detail.html');
    }
    /**
     *  filter goods desc.
     */
    private function html_filter($html)
    {
        $html_filter_conf = new HTMLFilterConfiguration();
        $allow_tag = array('p','br','ul','li','ol','table','tr','td','th','tfoot','thead','img');
        foreach ($allow_tag as $tag_name) {
            $html_filter_conf->allowTag($tag_name);
        }
        $html_filter_conf->allowAttribute('img', 'src');
        $html_filter = new HTMLFilter();
        $return = $html_filter->filter($html_filter_conf, $html);
        $return = preg_replace("/&#([\d]+);/","", $return);
        return $return;
    }
}
