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


class wechat_ctl_mobile_wxqrlogin extends mobile_controller
{
    public $title = '微信安全二维码登录';
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
    }

    public function dologin()
    {
        $enc_str = $_GET['enc_str'];
        $enc_str = app::get('mobile')->router()->decode_args($enc_str);
        $member_id = $_GET['mid'];
        $decode_enc = utils::decrypt($enc_str);
        $session_id = $decode_enc['session_id'];
        if ($session_id) {
            $session_arr = explode('|',$session_id);
            $session_id = $session_arr[0];
            $time = $session_arr[1];
            if((time() - (int)$time) > 3600){ //超时
                logger::error('微信登录失败!超时.'.var_export($_GET,1).var_export($decode_enc,1));
                $this->display('mobile/wxloginerror.html');exit;
            }
            vmc::singleton('base_session')->set_sess_id($session_id);
            vmc::singleton('base_session')->start();
            vmc::singleton('b2c_user_object')->set_member_session($member_id);
            $member_id = vmc::singleton('b2c_user_object')->get_member_session();
            $member = app::get('b2c')->model('members')->dump($member_id);
            if ($member) {
                $this->pagedata['member'] = $member;
                $this->display('mobile/wxloginsuccess.html');
            } else {
                logger::error('微信登录失败!未知会员数据.'.var_export($_GET,1).var_export($decode_enc['session_id'],1));
                $this->display('mobile/wxloginerror.html');
            }
        } else {
            logger::error('微信登录失败!未知SESSION_ID.'.var_export($_GET,1).var_export($decode_enc['session_id'],1));
            $this->display('mobile/wxloginerror.html');
        }
    }
}
