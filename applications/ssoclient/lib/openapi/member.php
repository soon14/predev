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
class ssoclient_openapi_member extends base_openapi{

    public function __construct(){
        $this->params = vmc::singleton('base_component_request')->get_params(true);
    }

    public function login(){
        $sso_uid = $this->params['vmc_uid'];
        $token = $this->params['vmc_utoken'];
        if( vmc::singleton('ssoclient_member_sso') ->member_verify($token ,$sso_uid)){
            $this ->success('登录成功');
        }
        $this ->failure('登录失败');
    }

    public function logout(){
        vmc::singleton('ssoclient_member_sso') ->logout();
        $this ->success('注销成功');
    }

}