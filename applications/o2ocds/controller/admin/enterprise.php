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
class o2ocds_ctl_admin_enterprise extends desktop_controller {

    public function __construct($app)
    {
        parent::__construct($app);
        if(!$this->has_permission('o2ocds_enterprise')){
            die('Permission ERROR');
        }
    }

    public function index() {
        if($this->has_permission('o2ocds_add_enterprise')) {
            $custom_actions[] = array(
                'label' => ('添加企业') ,
                'icon' => 'fa-plus',
                'href' => 'index.php?app=o2ocds&ctl=admin_enterprise&act=edit',
            );
        }
        $custom_actions[] = array(
            'label' => ('企业注册二维码') ,
            'icon'=>'fa-qrcode',
            'class'=>'btn-default',
            'href' => 'index.php?app=o2ocds&ctl=admin_enterprise&act=qrcode&p[0]='.'https://vmc7b5621.v1.wdwdwd.com/m/qrrouter.html?signup=enterprise'.'&singlepage=1',
            'target' => '_blank',
            );
        $this->finder('o2ocds_mdl_enterprise',array(
            'title' => '企业列表',
            'actions' => $custom_actions,
            'use_buildin_recycle' => $this->has_permission('o2ocds_delete_enterprise'),
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
        ));
    }

    /*
     * 添加编辑页
     * @params 企业id
     * */
    public function edit($enterprise_id) {
        if($enterprise_id) {
            $mdl_enterprise = $this->app->model('enterprise');
            $enterprise = $mdl_enterprise->dump($enterprise_id);
            $this->pagedata['enterprise'] = $enterprise;
            if($store_list = $mdl_enterprise->relevance_store(array('enterprise'=>$enterprise),0,-1,true)) {
                $this->pagedata['store_list'] = $store_list;
            };
            if($sales_list = $mdl_enterprise->relation_sales($enterprise_id)) {
                $this->pagedata['sales_list'] = $sales_list;
            };
        }
        $this->page('admin/enterprise/edit.html');
    }

    /**
     * 店铺相关订单
     */
    public function order($enterprise_id, $page = 1, $pagelimit = 1)
    {
        $mdl_order = app::get('b2c')->model('orders');
        $mdl_achieve = $this->app->model('orderlog_achieve');
        $filter = array('relation_id'=>$enterprise_id,'type'=>'enterprise');
        if($order_ids = $mdl_achieve->getList('order_id',$filter,$pagelimit * ($page - 1), $pagelimit)) {
            $order_ids = array_keys(utils::array_change_key($order_ids,'order_id'));
        };
        $count = $mdl_achieve->count($filter);
        $orders = $mdl_order->getList('*', array('order_id' => $order_ids));
        foreach ($orders as $key => $row) {
            $orders[$key]['order_status_label'] = vmc::singleton('b2c_finder_orders')->column_orderstatus($row);
        }
        $this->pagedata['orders'] = $orders;
        $this->pagedata['pager'] = array(
            'current' => $page,
            'total' => ceil($count / $pagelimit) ,
            'link' => 'index.php?app=o2ocds&ctl=admin_enterprise&act=order&p[0]='.$enterprise_id.'&p[1]='.time() ,
            'token' => time(),
        );
        $this->display('admin/enterprise/order.html');
    }

    public function save() {
        $this->begin();
        $enterprise = $_POST['info'];
        $mdl_enterprise = $this->app->model('enterprise');

        if(!$mdl_enterprise->check($enterprise,$msg)) {
            $this->end(false,$msg);
        };
        //操作日志
        $obj_log = vmc::singleton('o2ocds_operatorlog');
        if(method_exists($obj_log,'enterprise_log')) {
            $obj_log->enterprise_log($enterprise);
        }
        if(!$mdl_enterprise->save($enterprise) ) {
            $this->end(false,'操作失败');
        }

        $this->end(true,'操作成功',null,array('enterprise_id'=>$enterprise['enterprise_id']));
    }

    /*
     * 编辑员工关系
     * */
    public function update_relation($enterprise_id,$member_id,$relation) {
        $this->begin('index.php?app=o2ocds&ctl=admin_enterprise&act=edit&tab=2&p[0]='.$enterprise_id);
        if(!$enterprise_id || !$member_id ||!$relation || !in_array($relation,array(
            'admin','salesman'
        ))){
            $this->end(false,'参数异常');
        }
        $mdl_relation = $this->app->model('relation');
        if(!$mdl_relation->count(array('relation_id'=>$enterprise_id,'member_id'=>$member_id))){
            $this->end(false,'参数异常');
        }
        //操作日志
        $obj_log = vmc::singleton('o2ocds_operatorlog');
        if(method_exists($obj_log,'relation_log')) {
            $obj_log->relation_log(array('enterprise_id'=>$enterprise_id,'member_id'=>$member_id,'relation'=>$relation),'update');
        }
        $this->end($mdl_relation->update(array(
            'relation'=>$relation
        ),array(
            'relation_id'=>$enterprise_id,
            'type'=>'enterprise',
            'member_id'=>$member_id
        )));
    }
    public function delete_relation($enterprise_id,$member_id) {
        $this->begin('index.php?app=o2ocds&ctl=admin_enterprise&act=edit&tab=2&p[0]='.$enterprise_id);
        if(!$enterprise_id || !$member_id){
            $this->end(false,'参数异常');
        }
        $mdl_relation = $this->app->model('relation');
        if(!$mdl_relation->count(array('relation_id'=>$enterprise_id,'member_id'=>$member_id))){
            $this->end(false,'参数异常');
        }
        //操作日志
        $obj_log = vmc::singleton('o2ocds_operatorlog');
        if(method_exists($obj_log,'relation_log')) {
            $obj_log->relation_log(array('enterprise_id'=>$enterprise_id,'member_id'=>$member_id),'delete');
        }
        $this->end($mdl_relation->delete(array(
            'relation_id'=>$enterprise_id,
            'type'=>'enterprise',
            'member_id'=>$member_id
        )));
    }
    public function add_relation($enterprise_id,$member_id) {
        $this->begin('index.php?app=o2ocds&ctl=admin_enterprise&act=edit&tab=2&p[0]='.$enterprise_id);
        if(!$enterprise_id || !$member_id){
            $this->end(false,'参数异常');
        }
        $mdl_relation = $this->app->model('relation');
        if($mdl_relation->count(array('relation_id'=>$enterprise_id,'member_id'=>$member_id))){
            $this->end(false,'重复的账号');
        }
        //操作日志
        $obj_log = vmc::singleton('o2ocds_operatorlog');
        if(method_exists($obj_log,'relation_log')) {
            $obj_log->relation_log(array('enterprise_id'=>$enterprise_id,'member_id'=>$member_id,'relation'=>'salesman'),'add');
        }
        $new_relation = array(
            'relation_id'=>$enterprise_id,
            'member_id'=>$member_id,
            'type'=>'enterprise',
            'relation'=>'salesman',//默认不给管理权限
            'time'=>time()
        );
        $this->end($mdl_relation->save($new_relation));
    }

    public function qrcode($url) {
        // image  response
        ectools_qrcode_QRcode::png($url,false,0,7,10);
    }


}
