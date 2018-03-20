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


class openidcheck_ctl_mobile_openidcheck extends mobile_controller
{

    public function __construct($app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->user_obj = vmc::singleton('b2c_user_object');
        if(!$this->user_obj->is_login()){
            exit;
        }else{
            $this->member_id = $this->user_obj->get_member_id();
        }
    }
    public function index($forward)
    {
        $this->title = "绑定手机";
        $user_obj = vmc::singleton('b2c_user_object');
        $redirect_member_index = array('app' => 'b2c','ctl' => 'site_member');
        $pam_data = $user_obj->get_pam_data('*', $this->member_id);
        if($pam_data['mobile']){
            $this->splash('success',$redirect_member_index,'已绑定手机');
        }
        if (!$forward) {
            $forward = $_SERVER['HTTP_REFERER'];
        }
        $this->pagedata['forward'] = $forward;
        $this->pagedata['member'] = $this->member;
        $this->pagedata['pam_data'] = $pam_data;
        $pam_data_schema = app::get('pam')->model('members')->get_schema();
        $this->pagedata['pam_type'] = $pam_data_schema['columns']['login_type']['type'];
        $this->page('mobile/signup.html');
    }

    public function dosign(){
        $this->begin();
        $forward = $_POST['forward'];
        if(!$forward){
            $forward = $this->gen_url(array(
                'app'=>'b2c',
                'ctl'=>'mobile_member',
                'act'=>'index'
            ));
        }
        $mobile = $_POST['mobile'];
        $new_password = $_POST['password'];
        $vcode = $_POST['vcode'];
        if(!vmc::singleton('b2c_user_vcode')->verify($vcode, $mobile, 'signup')){
            $this->end(false,'验证码不正确');
        }
        if(!vmc::singleton('b2c_user_passport')->set_mobile($mobile,$msg)){
            $this->end(false,$msg);
        }else{
            //更新密码
            if(!vmc::singleton('b2c_user_passport')->reset_password($this->member_id,$new_password)){
                $this->end(false,'密码设置失败');
            }
            $this->end_only();
            $db = vmc::database();
            $db->commit($this->transaction_status);
            $this->redirect($forward);
        }
    }

    public function ignore($forward){
        $_SESSION['openidcheck']['ignore'] = 'true';
        $this->redirect($forward);
    }

}
