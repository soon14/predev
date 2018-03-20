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


class store_ctl_admin_store extends store_ctl_admin_controller {
    public function __construct($app) {
        parent::__construct($app);

        //必须先选择店铺
        //$this->select_store();
    }

    function index() {
        $this->finder('store_mdl_store', array(
            'title' => ('门店列表') ,
            'actions' => array(
                array(
                    'label' => ('添加门店') ,
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=store&ctl=admin_store&act=edit',
                ) ,
            )
        ));
    }

    /**
     * 保存门店信息方法
     */
    function save() {
        $this->begin('index.php?app=store&ctl=admin_store&act=index');
        $data = $_POST;
        $mdl_store = $this->app->model('store');
        if ($mdl_store->save($data, $msg)) {
            $this->end(true, '保存成功');
        } else {
            $this->end(false, $msg);
        }
    }

    /**
     * 编辑店铺页面
     *
     * @param $store_id
     */
    function edit($store_id) {
        //查询店铺信息
        $mdl_store = $this->app->model('store');
        $this->pagedata['store'] = $mdl_store->dump($store_id);
        $users = $this ->app ->model('relation_desktopuser') ->getList('user_id' ,array('store_id' =>$store_id));
        $this->pagedata['user_list'] = app::get('desktop')->model('users')->getList('*' ,array('user_id'=>array_keys(utils::array_change_key($users ,'user_id'))));

        $this->page('admin/store/edit.html');
    }

    /**
     * ajax获取店铺操作员详细信息
     */
    public function ajax_store_users()
    {
        $this->pagedata['user_list'] = app::get('desktop')->model('users')->getList('user_id, name, op_no, memo', array('user_id' => $_POST['user_id']));
        $this->display('admin/store/ajax_user_item.html');
    }

}
