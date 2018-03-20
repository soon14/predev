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


class ssoclient_ctl_mobile_passport extends mobile_controller
{
    public $title = '账户';
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this ->base_server_url = SSO_SERVER;
    }


    public function index(){
        $this ->login();
    }

    public function login(){
        $forward = $_GET['forward'];
        $next_action =$this ->gen_url(array(
            'app' =>'ssoclient',
            'ctl' =>'mobile_passport',
            'act' =>'to_redirect',
            'full' =>1
        ));
        $next_action .= '?forward='.$forward;
        $next = urlencode($next_action);
        $login_url = $this ->base_server_url.'sso-login.html?forward='.$next;
        $this ->redirect($login_url);
    }

    public function post_login(){
        $sso_uid = $_POST['vmc_uid'];
        $token = $_POST['vmc_utoken'];
        if( vmc::singleton('ssoclient_member_sso') ->member_verify($token ,$sso_uid)){
            $this ->splash('success' ,'' ,'登录成功');
        }
        $this ->splash('error' ,'' ,'登录失败');
    }

    public function signup(){
        $forward = $_GET['forward'];
        $next_action =$this ->gen_url(array(
            'app' =>'ssoclient',
            'ctl' =>'mobile_passport',
            'act' =>'to_redirect',
            'full' =>1
        ));
        $next_action .= '?forward='.$forward;
        $next = urlencode($next_action);
        $signup_url = $this ->base_server_url.'sso-signup.html?forward='.$next;
        $this ->redirect($signup_url);
    }

    public function logout()
    {
        $forward = $_GET['forward'];
        $this->unset_member();
        if (!$forward) {
            $forward = $this->gen_url(array(
                'app' => 'mobile',
                'ctl' => 'index',
                'full' => 1,
            ));
        }
        $this->splash('success', $forward, '退出登录成功');
    }

    public function to_redirect(){
        $forward = $_GET['forward'];
        if(!$forward){
            $forward = $this->gen_url(array(
                'app' => 'mobile',
                'ctl' => 'index',
                'full' => 1,
            ));
        }
        $this ->redirect($forward);
    }

    private function unset_member()
    {
        vmc::singleton('ssoclient_member_sso') ->logout();
    }
}
