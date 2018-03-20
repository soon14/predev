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
class commission_service_member_browse
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->_request = vmc::singleton('base_component_request');
    }

    public function exec(&$request ,$redirect = true){
        if(!app::get('commission') ->getConf('sub_domain')){//不启用二级域名
            $redirect = false;
        }
        if(!$this->_request->get_ctl_name()){//非控制器，不跳转
            $redirect = false;
        }
        if($redirect){
            $from_uri = $this->_request->get_request_uri();
            $root_domain = app::get('commission') ->getConf('root_domain');
            if($this->need_root_domain()){
                if($_SERVER['HTTP_HOST'] !=$root_domain){
                    $url = ($_SERVER['REQUEST_SCHEME']?$_SERVER['REQUEST_SCHEME']:'http').'://'.$root_domain.($_SERVER['SERVER_PORT'] ==80 ?'':$_SERVER['SERVER_PORT']).$from_uri;
                    header('Location:'.$url);exit;
                }
            }else{
                preg_match('/^(\w+)\.\w+\.\w+[:\d+]*$/', $_SERVER['HTTP_HOST'], $matches);
                if($matches[1]){
                    foreach(file($this ->app->app_dir.'/keep_domain') as $row){
                        $domain_arr = explode(" " , trim($row));
                        if(!in_array($matches[1] ,$domain_arr ,true)){
                            setcookie('dp' ,$matches[1] ,null ,'/' ,COOKIE_DOMAIN);
                        }
                    }
                }
                vmc::singleton('commission_service_member')->redirect($from_uri);
            }
        }else{
            if($from_id = $this->_request->get_get('fmid')){//from member id
                setcookie('fmid' ,$from_id ,null ,'/' );
            }
        }
    }
    public function need_root_domain(){
        //微信支付
        if (base_mobiledetect::is_wechat() && $this->_request->get_act_name() == 'dopayment'){
            return true;
        }
        //微信扫码登录
        if ($this->_request->get_ctl_name() == 'site_wxqrlogin'){
            return true;
        }
        return false;
    }
}