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

class codebuy_ctl_admin_activity extends desktop_controller
{
    public function index()
    {
        $this->finder('codebuy_mdl_activity', array(
            'title' => ('优购码活动列表') ,
            'use_buildin_recycle'=>true,
            'actions' => array(
                array(
                    'label' => ('添加优购码活动') ,
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=codebuy&ctl=admin_activity&act=add',
                ) ,
            ),
            'finder_extra_view'=>array(array('app'=>'codebuy','view'=>'/admin/create_code.html'))
        ));
    }
    public function add(){
        $this->page('admin/edit.html');
    }
    public function edit($id){
        $mdl_activity = $this->app->model('activity');
        $mdl_goods = app::get('b2c')->model('goods');
        $activity = $mdl_activity->getRow('*',array('id'=>$id));
        $this->pagedata['activity'] = $activity;
        $this->pagedata['goods'] = $mdl_goods->getRow('goods_id,name,gid,image_default_id',array('goods_id'=>$activity['goods_id']));
        $this->page('admin/edit.html');
    }
    public function save(){
        $mdl_activity = $this->app->model('activity');
        $this->begin();
        $_POST['start'] = strtotime($_POST['start']);
        $_POST['end'] = strtotime($_POST['end']);
        $msg = '保存成功';
        if($mdl_activity->save($_POST,null,false,$msg)){
            $this->end(true);
        }else{
            $this->end(false,$msg);
        }
    }
    //ajax异步拉取商品数据
    public function get_goods(){
        $gids = $_POST['goods_id'];
        $this->pagedata['goods'] = app::get('b2c')->model('goods')->getRow('goods_id,name,image_default_id,gid', array(
            'goods_id' => $gids,
        ));
        $this->display('admin/get_goods.html');
    }
    //生成优购码
    public function create_code(){
        $this->begin();
        if(!$_POST['nums']){
            $this->end(false,'请输入优购码数量');
        }
        $mdl_activity = $this->app->model('activity');
        $mdl_code = $this->app->model('code');
        $op_name = $this->user->get_login_name();
        $flag = $mdl_code->generateCode($_POST['activity_id'],$_POST['nums'],$op_name,$_POST['remark']);
        if($flag){
            $this->end(true);
        }else{
            $this->end(false);
        }
    }
}
