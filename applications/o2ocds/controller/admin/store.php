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
class o2ocds_ctl_admin_store extends desktop_controller {

    public function __construct($app)
    {
        parent::__construct($app);
        if(!$this->has_permission('o2ocds_store')){
            die('Permission ERROR');
        }
    }

    public function index() {
        if($this->has_permission('o2ocds_add_store')) {
            $custom_actions[] = array(
                'label' => ('添加店铺') ,
                'icon' => 'fa-plus',
                'href' => 'index.php?app=o2ocds&ctl=admin_store&act=edit',
            );
        }
        $this->finder('o2ocds_mdl_store',array(
            'title' => '店铺列表',
            'actions' => $custom_actions,
            'use_buildin_set_tag' => true,
            'use_buildin_recycle' => $this->has_permission('o2ocds_delete_store'),
            'use_buildin_filter' => true,
        ));
    }

    /*
     * 新增编辑页
     * @params 店铺id
     * */
    public function edit($store_id) {
        if($store_id) {
            $mdl_store = $this->app->model('store');
            $store = $mdl_store->dump($store_id);
            $this->pagedata['store'] = $store;
            if($clerk_list = $mdl_store->relation_clerk($store['store_id'])) {
                $this->pagedata['clerk_list'] = $clerk_list;
            }
            $mdl_service_code = $this->app->model('service_code');
            $filter = array('store_id'=>$store_id);
            $orders = $mdl_service_code->order_list($filter,array(), 0, 10, 'o.createtime DESC', $count);
            foreach ($orders as $key => $row) {
                $orders[$key]['order_status_label'] = vmc::singleton('b2c_finder_orders')->column_orderstatus($row);
            }
            $this->pagedata['orders'] = $orders;
            $this->pagedata['pager'] = array(
                'current' => 1,
                'total' => ceil($count / 10) ,
                'link' => 'index.php?app=o2ocds&ctl=admin_store&act=order&p[0]='.$store_id.'&p[1]='.time() ,
                'token' => time(),
            );
        }
        $this->page('admin/store/edit.html');
    }

    /**
     * 店铺相关订单
     */
    public function order($store_id, $page = 1, $pagelimit = 10)
    {
        $mdl_service_code = $this->app->model('service_code');
        $filter = array('store_id'=>$store_id);
        $orders = $mdl_service_code->order_list($filter,array(), $pagelimit * ($page - 1), $pagelimit, 'o.createtime DESC', $count);
        foreach ($orders as $key => $row) {
            $orders[$key]['order_status_label'] = vmc::singleton('b2c_finder_orders')->column_orderstatus($row);
        }

        $this->pagedata['orders'] = $orders;
        $this->pagedata['pager'] = array(
            'current' => $page,
            'total' => ceil($count / $pagelimit) ,
            'link' => 'index.php?app=o2ocds&ctl=admin_store&act=order&p[0]='.$store_id.'&p[1]='.time() ,
            'token' => time(),
        );
        $this->display('admin/store/order.html');
    }

    /*
     * 保存店铺信息
     * */
    public function save() {
        $this->begin();
        $store = $_POST['info'];
        $mdl_store = $this->app->model('store');
        if($store['images']) {
            $images = array();
            foreach ((array) $store['images'] as $imageId) {
                $images[] = array(
                    'target_type' => 'o2ocds_store',
                    'image_id' => $imageId,
                );
            }
            $store['images'] = $images;
            unset($images);
        }
        if(!$mdl_store->check($store,$msg)) {
            $this->end(false,$msg);
        };
        //操作日志
        $obj_log = vmc::singleton('o2ocds_operatorlog');
        if(method_exists($obj_log,'store_log')) {
            $obj_log->store_log($store);
        }
        if(!$mdl_store->save($store) ) {
            $this->end(false,'操作失败');
        }
        $this->end(true,'操作成功',null,array('store_id'=>$store['store_id']));
    }

    /*
     * 编辑店员关系
     * */
    public function update_relation($store_id,$member_id,$relation) {
        $this->begin('index.php?app=o2ocds&ctl=admin_store&act=edit&tab=2&p[0]='.$store_id);
        if(!$store_id || !$member_id ||!$relation || !in_array($relation,array(
            'manager','salesclerk'
        ))){
            $this->end(false,'参数异常');
        }
        $mdl_relation = $this->app->model('relation');
        if(!$mdl_relation->count(array('relation_id'=>$store_id,'member_id'=>$member_id))){
            $this->end(false,'参数异常');
        }

        //操作日志
        $obj_log = vmc::singleton('o2ocds_operatorlog');
        if(method_exists($obj_log,'relation_log')) {
            $obj_log->relation_log(array('store_id'=>$store_id,'member_id'=>$member_id,'relation'=>$relation),'update');
        }

        $this->end($mdl_relation->update(array(
            'relation'=>$relation
        ),array(
            'relation_id'=>$store_id,
            'type'=>'store',
            'member_id'=>$member_id
        )));
    }
    public function delete_relation($store_id,$member_id) {
        $this->begin('index.php?app=o2ocds&ctl=admin_store&act=edit&tab=2&p[0]='.$store_id);
        if(!$store_id || !$member_id){
            $this->end(false,'参数异常');
        }
        $mdl_relation = $this->app->model('relation');
        if(!$mdl_relation->count(array('relation_id'=>$store_id,'member_id'=>$member_id))){
            $this->end(false,'参数异常');
        }
        //操作日志
        $obj_log = vmc::singleton('o2ocds_operatorlog');
        if(method_exists($obj_log,'relation_log')) {
            $obj_log->relation_log(array('store_id'=>$store_id,'member_id'=>$member_id),'delete');
        }
        $this->end($mdl_relation->delete(array(
            'relation_id'=>$store_id,
            'type'=>'store',
            'member_id'=>$member_id
        )));
    }
    public function add_relation($store_id,$member_id) {
        $this->begin('index.php?app=o2ocds&ctl=admin_store&act=edit&tab=2&p[0]='.$store_id);
        if(!$store_id || !$member_id){
            $this->end(false,'参数异常');
        }
        $mdl_relation = $this->app->model('relation');
        if($mdl_relation->count(array('relation_id'=>$store_id,'member_id'=>$member_id))){
            $this->end(false,'重复的账号');
        }
        //操作日志
        $obj_log = vmc::singleton('o2ocds_operatorlog');
        if(method_exists($obj_log,'relation_log')) {
            $obj_log->relation_log(array('store_id'=>$store_id,'member_id'=>$member_id,'relation'=>'salesclerk'),'add');
        }
        $new_relation = array(
            'relation_id'=>$store_id,
            'member_id'=>$member_id,
            'type'=>'store',
            'relation'=>'salesclerk',//默认不给管理权限
            'time'=>time()
        );
        $this->end($mdl_relation->save($new_relation));
    }

}
