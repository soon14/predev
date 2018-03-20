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


class wechat_ctl_site_wxqrlogin extends site_controller
{
    public $title = '微信安全二维码登录';
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
    }

    public function index($enc_str)
    {
        $app_define = utils::decrypt($enc_str);
        $action_url = urldecode($action_url);
        $obj_wstage = vmc::singleton('wechat_stage');
        $access_token = $obj_wstage->get_access_token(false, $app_define);
        $app_id = $app_define['app_id'];
        vmc::singleton('base_session')->start();
        $session_str = utils::encrypt(array(
            'session_id' => vmc::singleton('base_session')->sess_id().'|'.time(),
        ));
        $session_str = app::get('mobile')->router()->encode_args($session_str);
        $redirect_uri = vmc::openapi_url('openapi.toauth', 'callback', array(
            'wechat_toauth_pam' => 'callback',
        )).'?qrlp='.$session_str;
        $forward = $_GET['forward'];
        $state = app::get('mobile')->router()->gen_url(array('app' => 'wechat',
        'ctl' => 'mobile_wxqrlogin',
        'act' => 'dologin'
        ));
        $long_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$app_id&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=$state#wechat_redirect";

        if (!$access_token) {
            $this->splash('error', '', '二维码生成失败');
        }
        if ($surl = $obj_wstage->gen_surl($long_url, $access_token, $msg)) {
            $this->pagedata['surl'] = $surl;
        } else {
            $this->splash('error', '', '二维码生成失败:'.$msg);
        }
    
        $this->pagedata['forward'] = $forward;
        $this->page('site/loginqrcode.html');
    }

    public function watch_login()
    {
        if ($member_id = vmc::singleton('b2c_user_object')->get_member_session()) {
            vmc::singleton('b2c_frontpage')->bind_member($member_id);
            echo 'success';
        }
    }
}
