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


class o2ocds_mfrontpage extends mobile_controller {

    protected $o2ocds;
    function __construct(&$app) {
        parent::__construct($app);
    }

    function verify_o2ocds_member() {
        $user_obj = vmc::singleton('b2c_user_object');
        if ($this->app->member_id = $user_obj->get_member_id()) {
            $member_id = $this->app->member_id;
            $mdl_relation = $this->app->model('relation');

            if($relation_enterprise = $mdl_relation->getRow('*',array('type'=>'enterprise','member_id'=>$member_id))) {
                $mdl_enterprise = $this->app->model('enterprise');
                if(!$enterprise = $mdl_enterprise->getRow('*',array('enterprise_id'=>$relation_enterprise['relation_id']))) {
                    $this->splash('error', '', '企业数据异常');
                };
                $this->app->enterprise = $enterprise;
                $this->app->relation = $relation_enterprise['relation'];
                $this->app->type = $relation_enterprise['type'];
                return true;
            }


            if($relation_store = $mdl_relation->getList('*',array('type'=>'store','member_id'=>$member_id))) {
                $store_ids = array_keys(utils::array_change_key($relation_store,'relation_id'));
                $mdl_store = $this->app->model('store');
                $store_list = $mdl_store->getList('*',array('store_id'=>$store_ids));
                if(!$store_list){
                    $this->splash('error', '', '店铺数据异常');
                }else{
                    //店长身份
                    if($store_manager = $mdl_relation->getRow('*',array('type'=>'store','member_id'=>$member_id,'relation'=>'manager'))) {
                        $this->app->relation = $store_manager['relation'];
                        $this->app->type = $store_manager['type'];
                        $this->app->store_manager = $store_manager;
                    }else{
                        $this->app->relation = $relation_store[0]['relation'];
                        $this->app->type = $relation_store[0]['type'];
                        $this->app->store = $relation_store[0];
                    }
                    $this->app->store_list = $store_list;
                    //$this->app->store_ids = array_keys(utils::array_change_key($this->store_list,'store_id'));
                }
                return true;
            }

            $this->splash('error', null, '无权访问分销管理');
        }
        $this->splash('error',null, '无权限');
    }


}
