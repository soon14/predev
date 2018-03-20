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

class o2ocds_ctl_mobile_qrrouter extends b2c_mfrontpage
{
    //我的分享二维码
    public function index(){
        //setcookie('qrcode','test00110001');
        $qrcode = $_GET['qrcode'];
        $mdl_qrcode = $this->app->model('qrcode');
        if(!$qrcode_id = $mdl_qrcode->get_qrcode_id($qrcode,$msg)) {
            $this->splash('error',null,$qrcode.' 未知二维码');
        };
        if(!$qrcode_data = $mdl_qrcode->getRow('*',array('qrcode_id'=>$qrcode_id))) {
            $this->splash('error',null,$qrcode.' 未知二维码');
        }else{
            if($qrcode_data['status'] == '0') {
                $msg = '未登陆';
            }elseif($qrcode_data['status'] == '1'){
                $this->splash('error',null,$qrcode.' 已登陆');
            }
        };
        $is_wechat = base_mobiledetect::is_wechat();
        $is_wechat && $ver_count = intval(implode('', explode('.', $is_wechat)));
        if($ver_count<656){
            $this->splash('error',null,$qrcode.'微信版本过低,请升级微信到最新版');
        }
        $this->splash('success',null,$qrcode. $msg);
    }


    public function bind_relation() {

        echo $_GET['bind_relation'];
    }



}