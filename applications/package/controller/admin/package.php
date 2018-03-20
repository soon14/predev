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

class package_ctl_admin_package extends desktop_controller
{
    public function index()
    {
        $this->finder('package_mdl_package', array(
            'title' => ('组合套餐活动列表') ,
            'use_buildin_recycle'=>true,
            'actions' => array(
                array(
                    'label' => ('添加组合套餐活动') ,
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=package&ctl=admin_package&act=add',
                ) ,
            ),
        ));
    }
    public function add(){
        $this->page('admin/edit.html');
    }
    public function edit($id){
        $mdl_package = $this->app->model('package');
        $mdl_goods = app::get('b2c')->model('goods');
        $package = $mdl_package->getRow('*',array('id'=>$id));
        $this->pagedata['package'] = $package;
        $this->pagedata['goods'] = $mdl_goods->getList('goods_id,name,gid,image_default_id',array('goods_id'=>$package['goods_id']));
        $package_goods = $package['package_goods'];
        foreach($package_goods as $key=>$item){
            $tmp_goods = $mdl_goods->getRow('name,gid',array('goods_id'=>$item['goods_id']));
            $package_goods[$key]['name'] = $tmp_goods['name'];
            $package_goods[$key]['gid'] = $tmp_goods['gid'];
        }
        vmc::singleton('b2c_goods_stage')->gallery($package_goods);
        $this->pagedata['package_goods'] = $package_goods;
        $this->page('admin/edit.html');
    }
    public function save(){
        $mdl_package = $this->app->model('package');
        $this->begin();
        $_POST['start'] = strtotime($_POST['start']);
        $_POST['end'] = strtotime($_POST['end']);
        $msg = '保存成功';
        $package_data = $_POST;
        if($mdl_package->save($package_data)){
            $this->end(true);
        }else{
            $this->end(false,$msg);
        }
    }
    //ajax异步拉取参与活动商品
    public function get_goods(){
        $gids = $_POST['goods_id'];
        $this->pagedata['goods'] = app::get('b2c')->model('goods')->getList('goods_id,name,image_default_id,gid', array(
            'goods_id' => $gids,
        ));
        $this->display('admin/get_goods.html');
    }
    //ajax异步拉取组合商品
    public function get_package_goods(){
        $gids = $_POST['goods_id'];
        $mdl_goods = app::get('b2c')->model('goods');
        $goods_list = $mdl_goods->getList('*',array('goods_id'=>$gids));
        vmc::singleton('b2c_goods_stage')->gallery($goods_list);
        $this->pagedata['package_goods'] = $goods_list;
        $this->display('admin/get_package_goods.html');
    }
}
