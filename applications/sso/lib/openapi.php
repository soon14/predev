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


class sso_openapi extends base_openapi
{
    private $params = array();
    public function __construct()
    {
            $this->params = vmc::singleton('base_component_request')->get_params(true);
    }
    /*
     * 跨域获取用户token
     */
    public function get_token(){
        $vmc_uid = $_COOKIE['vmc_uid'];
        $vmc_utoken = $_COOKIE['vmc_utoken'];
        if(!$vmc_uid || !$vmc_utoken){
            $this->_failure();
        }else{
            $this->_success(array('vmc_uid'=>$vmc_uid,'vmc_utoken'=>$vmc_utoken));
        }
    }
    /**
     * 检测用户登录情况
     */
    public function check_login(){
        if(!$this->params['vmc_uid'] || !$this->params['vmc_utoken']){
            $this->_failure('非法参数');
        }
        if(vmc::singleton('sso_action')->check_login($this->params['vmc_uid'],$this->params['vmc_utoken'])){
            $this->_success(array('login'=>true));
        }else{
            $this->_failure();
        }
    }
    /**
     * 退出登录
     */
    public function logout(){
        if(!$this->params['vmc_uid'] || !$this->params['vmc_utoken']){
            $this->_failure('非法参数');
        }
        if(vmc::singleton('sso_action')->logout($this->params['vmc_uid'],$this->params['vmc_utoken'])){
            $this->_success();
        }else{
            $this->_failure();
        }
    }
    /**
     * 获取用户信息
     */
    public function get_member(){
        if(!$this->params['vmc_uid'] || !$this->params['vmc_utoken']){
            $this->_failure('非法参数');
        }
        if(!vmc::singleton('sso_action')->check_login($this->params['vmc_uid'],$this->params['vmc_utoken'])){
            $this->_failure('用户未登录');
        }
        $mdl_member = app::get('b2c')->model('members');
        $member = $mdl_member->getRow('*',array('member_id'=>$this->params['vmc_uid']));
        $member['username'] = vmc::singleton('b2c_user_object')->get_member_name(null ,$this->params['vmc_uid']);
        $member['avatar'] =base_storager::image_path($member['avatar']);
        $this->_success($member);
    }
    /*正常返回*/
    private function _success($msg){
        if($this->params['callback']){
            $this->success_callback($this->params['callback'],$msg);
        }else{
            $this->success($msg);
        }
    }
    /*异常返回*/
    private function _failure($msg){
        if($this->params['callback']){
            $this->failure_callback($this->params['callback'],$msg);
        }else{
            $this->failure($msg);
        }
    }
}
