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


class sso_action
{
    //会员登录过期时间
    private $_token_expires = 1800;
    public function __construct()
    {
        $this->session = vmc::singleton('base_session');
        $this->session->start();
    }
    /*执行登录动作*/
    public function do_login(&$params,&$msg){
        //参数过滤，防止xss攻击
        $params = utils::_filter_input($params);
        $account_data = array(
            'login_account' => $params['uname'],
            'login_password' => $params['password'],
        );
        if (empty($params['vcode'])) {
            $msg = '请输入验证码';
            return false;
        }
        //尝试登陆
        $member_id = vmc::singleton('pam_passport_site_basic')->login($account_data, $params['vcode'], $msg);
        if (!$member_id) {
            return false;
        }
        $mdl_members = app::get('b2c')->model('members');
        $member_data = $mdl_members->getRow('member_lv_id,experience', array(
            'member_id' => $member_id,
        ));
        if (!$member_data) {
            $msg = '会员数据异常！';
            return false;
        }
        $member_data['order_num'] = app::get('b2c')->model('orders')->count(array(
            'member_id' => $member_id,
        ));

        //更新会员数据
        $mdl_members->update($member_data, array(
            'member_id' => $member_id,
        ));
        //设置登陆,并生成临时令牌
        $token = $this->_gen_token($member_id);
        $this->_set_sso_token($token,$member_id);
        $params['forward'] = $params['forward']?$params['forward']:BASE_URL;
        $msg = '登陆成功';
        return true;
    }
    /*退出*/
    public function logout($vmc_uid,$vmc_utoken){
        base_kvstore::instance('sso_member')->delete($vmc_utoken);
        return true;
    }
    /*创建用户*/
    public function create($params,&$msg = ''){
        $passport_obj = vmc::singleton('b2c_user_passport');
        $login_type = $passport_obj->get_login_account_type($params['pam_account']['login_name']);
        if ($login_type == 'mobile' && !vmc::singleton('b2c_user_vcode')->verify($params['vcode'], $params['pam_account']['login_name'], 'signup')) {
            $msg = '手机短信验证码不正确';
            return false;
        } elseif ($login_type != 'mobile' && !base_vcode::verify('passport', $params['vcode'])) {
            $msg = '验证码不正确';
            return false;
        }
        if (!$passport_obj->check_signup($params, $msg)) {
            return false;
        }
        $member_sdf_data = $passport_obj->pre_signup_process($params);

        if ($member_id = $passport_obj->save_members($member_sdf_data, $msg)) {
            //设置登陆,并生成临时令牌
            $token = $this->_gen_token($member_id);
            $this->_set_sso_token($token,$member_id);
            $msg = '注册成功';
            return true;
        } else {
            $msg = '注册失败,会员数据保存异常';
            return false;
        }
    }
    /*检测用户登录*/
    public function check_login($vmc_uid,$vmc_utoken){
        base_kvstore::instance('sso_member')->fetch($vmc_utoken,$member_id);
        if($member_id && $member_id == $vmc_uid){
            //延长时间
            $this->_set_sso_token($vmc_utoken,$member_id);
            return true;
        }else{
            return false;
        }
    }
    //设置sso令牌与uid
    private function _set_sso_token($token,$member_id){
        base_kvstore::instance('sso_member')->store($token,$member_id,$this->_token_expires);
        $cookie_expires = $this->_token_expires ? time() + $this->_token_expires * 60 : 0;
        $this->cookie_path = vmc::base_url() . '/';
        $this->_set_cookie('vmc_uid', $member_id, $cookie_expires);
        $this->_set_cookie('vmc_utoken', $token, $cookie_expires);
    }
    //浏览器端埋点
    private function _set_cookie($name, $value, $expire = false, $path = null) {
        $cookie_path = vmc::base_url() . '/';
        $domain = '.'.$_SERVER['SERVER_NAME'];
        $expire = $expire === false ? time() + 315360000 : $expire;
        setcookie($name, $value, $expire, $cookie_path,$domain);
        $_COOKIE[$name] = $value;
    }
    //生成临时令牌
    private function _gen_token($member_id){
        return md5(time().$member_id.$this->session->sess_id());
    }
}
