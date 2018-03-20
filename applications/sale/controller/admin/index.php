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

class sale_ctl_admin_index extends desktop_controller
{
    public function index()
    {
        $this->finder('sale_mdl_sales', array(
            'title' => ('商品预约') ,
            'use_buildin_recycle'=>true,
            'actions' => array(
                array(
                    'label' => ('添加预约') ,
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=sale&ctl=admin_index&act=add',
                ) ,
            )
        ));
    }
    public function add(){
        $this->page('admin/edit.html');
    }
    public function edit($id){
        $mdl_sales = $this->app->model('sales');
        $mdl_goods = app::get('b2c')->model('goods');
        $sales = $mdl_sales->getRow('*',array('id'=>$id));
        $this->pagedata['sale'] = $sales;
        $this->pagedata['goods'] = $mdl_goods->getRow('goods_id,name,gid,image_default_id',array('goods_id'=>$sales['goods_id']));
        $this->page('admin/edit.html');
    }
    public function save(){
        $this->begin();
        $mdl_sales = $this->app->model('sales');
        $_POST['reserve_start'] = strtotime($_POST['reserve_start']);
        $_POST['reserve_end'] = strtotime($_POST['reserve_end']);
        $_POST['start'] = strtotime($_POST['start']);
        $_POST['end'] = strtotime($_POST['end']);
        $_POST['alert'] = strtotime($_POST['alert']);
        $sale = $mdl_sales->getRow('id,name',array('id|noequal'=>$_POST['id'],'goods_id'=>$_POST['goods_id'],'status'=>'0'));
        if(!empty($sale)){
            $this->end(false,'此商品正在'.$sale['name'].'的预约活动中。');
        }
        if($mdl_sales->save($_POST)){
            $this->end(true);
        }else{
            $this->end(false);
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
}
