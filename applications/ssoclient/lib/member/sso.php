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
class ssoclient_member_sso{
    /**
     * @var
     */
    private $http_client ;
    /**sso 服务端地址
     * @var
     */
    private $base_server_url ;
    /**同顶级域名
     * @var bool
     */
    private $same_domain = false;
    /**用户token
     * @var
     */
    private $token;
    /**用户sso_uid
     * @var
     */
    private $sso_uid;
    /**用户sso信息
     * @var
     */
    private $sso_member;

    /**
     * @param $app
     */
    public function __construct($app){
        $this ->app = $app;
        $this ->base_server_url = SSO_SERVER.'openapi/sso/';
        $this ->http_client = vmc::singleton('base_httpclient');
        $this ->user_obj = vmc::singleton ('b2c_user_object');
        $this ->passport_obj = vmc::singleton ('b2c_user_passport');
    }

    /**用户sso 验证登录入口
     * @param null $token
     * @param null $sso_uid
     * @return bool
     */
    public function member_verify($token = null ,$sso_uid =null){
        $this ->_check_domain();
        $this ->token = $this ->same_domain ? $_COOKIE['vmc_utoken'] :$token;
        $this ->sso_uid = $this ->same_domain ? $_COOKIE['vmc_uid'] :$sso_uid;
        if ( !$this ->token || !$this ->sso_uid) {
            return false;
        }
        if($this ->user_obj->is_login ()){//登录状态
            return true;
        }
        if(!$this ->check_ticket ($this ->token  ,$this ->sso_uid)){
            $this ->unset_member();
            return false;
        }
        return $this ->_member_login();
    }

    /**
     * @param $token
     * @param $sso_uid
     * @return bool
     */
    public function check_ticket($token ,$sso_uid){
        $api = $this ->base_server_url."check_login";
        $res = $this ->http_client->post($api ,array(
            'vmc_utoken' =>$token,
            'vmc_uid' =>$sso_uid
        ));
        $re = json_decode($res ,true);
        if($re['result'] == 'success'){
            return true;
        }
        logger::error('check_ticket error:'.var_export($res ,1));
        return false;
    }

    /**
     * @param $token
     * @param $sso_uid
     * @return bool
     */
    public function get_member_info($token ,$sso_uid){
        $api = $this ->base_server_url."get_member";
        $res =$this ->http_client->post($api ,array(
            'vmc_utoken' =>$token,
            'vmc_uid' =>$sso_uid
        ));
        $re = json_decode($res ,true);
        if($re['result'] == 'success'){
            return $re['data'];
        }
        logger::error('get_member_info error:'.var_export($res ,1));
        return false;
    }


    /**用户登录入口，用户可能性判断
     * @return bool
     */
    private function _member_login(){
        //已注册
        if ($sso_data = $this->is_exist_sso ($this ->sso_uid)) {
            $this->_bind_member ($sso_data['member_id']);
            return true;
        }
        $this ->sso_member =  $member_info = $this->get_member_info ($this ->token ,$this ->sso_uid);
        if(!$member_info['username']){
            logger::error('用户数据不合法' .var_export($member_info ,1));
            return false;
        }
        //已注册，但sso信息未保存
        $account = app::get ('pam')->model ('members')->getRow ('*', array('login_account' => $member_info['username']));
        if ($account) {
            $this->_bind_member ($account['member_id']);
            $sso_data = array(
                'sso_uid' => $this ->sso_uid,
                'member_id' =>$account['member_id']
            );
            if (!$this->save_sso ($sso_data)) {
                logger::error ('sso会员数据保存异常');
            }
            return true;
        }
        //未注册
        $params = array(
            'pam_account' => array(
                'login_name' => $member_info['username'],
                'createtime' => time (),
            ),
            'b2c_members' => array(
                'contact' => array(
                    'email' => $member_info['email'],
                    'mobile' => $member_info['mobile']
                ),
                'avatar' => $member_info['avatar']
            )
        );
        $member_sdf_data = $this ->passport_obj->pre_signup_process ($params);
        if ($member_id = $this ->passport_obj->save_members ($member_sdf_data, $msg)) {
            $sso_data = array('sso_uid' => $this ->sso_uid, 'member_id' => $member_id);
            if (!$this->save_sso ($sso_data)) {
                logger::error ('sso会员数据保存异常');
            }
            $this->_bind_member ($member_id);
            /*本站会员注册完成后做某些操作!*/
            foreach (vmc::servicelist ('member.create_after') as $object) {
                $object->create_after ($member_id);
            }
            return true;
        } else {
            logger::error ('注册失败,会员数据保存异常' . $msg);
            return false;
        }
    }

    /**
     *判断当前是否和sso server同顶级域名
     */
    private function _check_domain(){
        $current_host = $_SERVER['HTTP_HOST'];
        preg_match('/(\w+?\.\w+).*/is' ,strrev($current_host) ,$current_res );
        $sso_host = parse_url($this ->base_server_url ,1);
        preg_match('/(\w+?\.\w+).*/is' ,strrev($sso_host) ,$host_res );
        if($current_res[1] == $host_res[1]){
            $this ->same_domain = true;
        }
    }

    /**
     * 设置用户登录session
     * @param $member_id
     */
    private function _bind_member ($member_id)
    {
        $columns = array(
            'members' => 'member_id,member_lv_id',
        );
        if($this ->sso_member['avatar']){
            $image_id = app::get('image') ->model('image') ->store($this ->sso_member['avatar'] ,md5($this ->sso_member['member']['avatar']));
            app::get('b2c') ->model('members') ->update(array('avatar' =>$image_id) ,array('member_id'=>$member_id));
        }
        $this ->user_obj->set_member_session($member_id);
        $cookie_expires = $this ->user_obj->cookie_expires ? time() + $this ->user_obj->cookie_expires * 60 : 0;
        $member_data = $this ->user_obj->get_members_data($columns,$member_id);
        $login_name = $this ->user_obj->get_member_name(null,$member_id);
        $cookie_path = vmc::base_url().'/';
        setcookie('vmc_utoken', $this ->token, $cookie_expires ,$cookie_path );
        setcookie('vmc_uid', $this ->sso_uid, $cookie_expires ,$cookie_path );
        setcookie('UNAME', $login_name, $cookie_expires ,$cookie_path );
        setcookie('MEMBER_IDENT', $member_id, $cookie_expires ,$cookie_path );
        setcookie('MEMBER_LEVEL_ID', $member_data['members']['member_lv_id'], $cookie_expires ,$cookie_path );
    }

    /**
     *清除用户登录状态
     */
    public function unset_member()
    {
        $account_type = pam_account::get_account_type(app::get('b2c')->app_id);
        unset($_SESSION['account'][$account_type]);
        app::get('b2c')->member_id = 0;
        $cookie_path = vmc::base_url().'/';
        foreach($_COOKIE as $key=>$value){
            setcookie($key,"",time()-60 ,$cookie_path);
        }
        foreach (vmc::servicelist('member.logout_after') as $service) {
            $service->logout();
        }
    }

    public function logout(){
        $api = $this ->base_server_url."logout";
        $res = $this ->http_client->post($api ,array(
            'vmc_utoken' =>$_COOKIE['vmc_utoken'],
            'vmc_uid' =>$_COOKIE['vmc_uid'],
        ));
        $re = json_decode($res ,true);
        if($re['result'] != 'success'){
            return false;
        }
        $this ->unset_member();
    }

    /**
     * @param $sso_uid
     * @return mixed
     */
    public function is_exist_sso($sso_uid){
        return $this ->app ->model('member_sso')->getRow('*',array('sso_uid' =>$sso_uid));
    }

    /**
     * @param $data
     * @return bool
     */
    public function save_sso($data){
        return $this ->app ->model('member_sso')->save($data);
    }
}