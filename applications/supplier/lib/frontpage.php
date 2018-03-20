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


class supplier_frontpage extends site_controller {


    protected $supplier;
    function __construct(&$app) {
        parent::__construct($app);
    }

    function verify_supplier_member() {
        $user_obj = vmc::singleton('b2c_user_object');
        if ($this->app->member_id = $user_obj->get_member_id()) {
            /*$data = $user_obj->get_members_data(array(
                'members' => 'member_id'
            ));*/
            $member_id = $this->app->member_id;
            if($member_id){
                $mdl_supplier = $this->app->model('supplier');
                $supplier = $mdl_supplier->getRow('*',array('member_id'=>$member_id,'supplier_status'=>'0'));
                if(!$supplier){
                    $this->splash('error', $this->gen_url(array(
                        'app' => 'site',
                        'ctl' => 'index',
                    )), '您还不是供应商,没有供应商后台权限.');
                }else{
                    $this->supplier = $supplier;
                }
                return true;
            }
        }
        $forward = $this->gen_url(array(
            'app'=>'supplier',
            'ctl'=>'site_supplier',
            'act'=>'index',
            'full'=>true
        ));
        if($this->_request->is_ajax()){
            $this->splash('error', $this->gen_url(array(
                'app' => 'b2c',
                'ctl' => 'site_passport',
                'act' => 'login',
                'args'=>array($forward)
            )), '未登录');
        }else{
            $this->redirect($this->gen_url(array(
                'app' => 'b2c',
                'ctl' => 'site_passport',
                'act' => 'login',
                'args'=>array($forward)
            )));
        }

    }


}
