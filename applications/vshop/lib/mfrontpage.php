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


class vshop_mfrontpage extends mobile_controller {

    protected $vshop;
    function __construct(&$app) {
        parent::__construct($app);
    }

    function verify_vshop_member() {
        $user_obj = vmc::singleton('b2c_user_object');
        if ($this->app->member_id = $user_obj->get_member_id()) {
            /*$data = $user_obj->get_members_data(array(
                'members' => 'member_id'
            ));*/
            $member_id = $this->app->member_id;
            if($member_id){
                $mdl_vshop = $this->app->model('shop');
                $vshop = $mdl_vshop->getRow('*',array('member_id'=>$member_id));
                if(!$vshop){
                    $this->splash('error', '', '您还不是微店主.');
                }else{
                    $mdl_vshop_lv = $this->app->model('lv');
                    $vshop['lv_info'] = $mdl_vshop_lv->dump($vshop['shop_lv_id']);
                    $vshop['logo_url'] = base_storager::image_path($vshop['logo'], 'm');
                    $vshop['banner_url'] = base_storager::image_path($vshop['gallery_image_id'], 'l');
                    $this->vshop = $vshop;
                }
                return true;
            }
        }
        $this->splash('error',null, '权限错误');

    }




}
