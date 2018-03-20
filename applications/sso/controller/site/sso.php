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


class sso_ctl_site_sso extends site_controller
{
    public $title = 'vmcshop单点登录';
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
    }
    /*
     * 如果是登录状态则直接跳转
     * */
    private function _check_login($vmc_uid,$vmc_utoken)
    {
        if(!$vmc_uid || !$vmc_utoken){
            return false;
        }
        if(vmc::singleton('sso_action')->check_login($vmc_uid,$vmc_utoken)){
            return true;
        }else{
            return false;
        }
    }
    /*登录页面*/
    public function login(){
        $this->title = '会员登录';
        $params = utils::_filter_input($_GET);
        $this->pagedata['forward'] = $params['forward']?$params['forward']:BASE_URL;
        if($this->_check_login($_COOKIE['vmc_uid'],$_COOKIE['vmc_utoken'])){
            $this->splash('success', $this->pagedata['forward'], '您已经登录');
        }
        $this->page('site/login.html');
    }
    /*注册页面*/
    public function signup(){
        $this->title = '会员注册';
        $params = utils::_filter_input($_GET);
        $this->pagedata['forward'] = $params['forward']?$params['forward']:BASE_URL;
        if($this->_check_login($_COOKIE['vmc_uid'],$_COOKIE['vmc_utoken'])){
            $this->splash('success', $this->pagedata['forward'], '您已经登录');
        }
        $this->page('site/signup.html');
    }
    /*执行登录动作*/
    public function do_login(){
        $params = $_POST;
        unset($_POST);
        if(vmc::singleton('sso_action')->do_login($params,$msg)){
            $this->splash('success', $params['forward'], '登录成功');
        }else{
            $this->splash('error', $params['sso_login'],  $msg);
        };
    }
    /*创建新用户*/
    public function create(){
        $params = $_POST;
        unset($_POST);
        if(vmc::singleton('sso_action')->create($params,$msg)){
            $this->splash('success', $params['forward'], '登录成功');
        }else{
            $this->splash('error',$params['sso_login'],  $msg);
        };
    }
}
