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

class experiencestore_ctl_admin_store extends desktop_controller
{
    public function index()
    {
        $this->finder('experiencestore_mdl_store', array(
            'title' => ('地点列表'),
            'use_buildin_recycle' => true,
            'actions' => array(
                array(
                    'label' => ('添加地点'),
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=experiencestore&ctl=admin_store&act=edit',
                ),
            ),
        ));
    }
    public function load($id){
        $store = $this->app->model('store')->dump($id);
        $store['image'] = base_storager::image_path($store['gallery_default_image_id'],'s');
        $store['region'] = vmc::singleton('experiencestore_view_helper')->modifier_regionpart($store['region']);
        $this->splash('success',null,'success','echo',array('store'=>$store));
    }
    public function edit($id)
    {
        if ($id) {
            $mdl_store = $this->app->model('store');
            $store = $mdl_store->dump($id,'*','default');
            $this->pagedata['store'] = $store;
        }
        $this->pagedata['lbs_openapi'] = vmc::openapi_url('openapi.tencentmap','view');
        $this->page('admin/store/edit.html');
    }
    public function save()
    {
        $this->begin('index.php?app=experiencestore&ctl=admin_store&act=index');
        $data = $_POST;
        $mdl_store = $this->app->model('store');
        $store = $data['store'];
        foreach ($store['images'] as $key=>&$value) {
            $value = array(
                'image_id'=>$value,
                'image_order'=>$key
            );
        }
        if ($mdl_store->save($store)) {
            $this->end(true, '保存成功');
        } else {
            $this->end(false, '保存失败');
        }
    }
}
