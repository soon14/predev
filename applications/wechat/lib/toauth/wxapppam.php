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


final class wechat_toauth_wxapppam extends toauth_abstract implements toauth_interface
{
    public $login_type = 'wechat';

    public $name = '微信小程序信任登录';
    public $version = '1.0';

    /**
     * 构造方法.
     *
     * @param null
     *
     * @return bool
     */
    public function __construct($app)
    {
        parent::__construct($app);
        if (!$this->login_type) {
            trigger_error('login_type 未定义!', E_USER_ERROR);
        }
        $this->callback_url = vmc::openapi_url('openapi.toauth', 'callback', array(
            __CLASS__ => 'callback',
        ));
        vmc::singleton('base_session')->start();
        $this->_response = vmc::singleton('base_component_response');
    }
    public function authorize_url()
    {
        return "__";
    }
    /**
     * 后台配置参数设置.
     *
     * @param null
     *
     * @return array 配置参数列表
     */
    public function setting()
    {
        return array(
            /*个性化字段开始*/
            'app_id' => array(
                'title' => 'APPID' ,
                'type' => 'text',
            ) ,
            'app_secret' => array(
                'title' => 'APPSECRET' ,
                'type' => 'text',
            ) ,
            'order_num' => array(
                'type' => 'hidden',
                'default' => 10,
            ) ,
            /*个性化字段结束*/
            'status' => array(
                'title' => '是否开启' ,
                'type' => 'radio',
                'options' => array(
                    'true' => '是' ,
                    'false' => '否' ,
                ) ,
                'default' => 'false',
            ),

        );
    }


    /**
     * 同步跳转处理.
     *
     * @see /applications/toauth/lib/api.php
     * @params array - 所有第三方回调参数，包括POST和GET
     */
    public function callback(&$params)
    {
        if($this->getConf('status', __CLASS__)!='true'){
            $this->_error('NOT ALLOW,status = false');
        }
        $code = $params['code'];
        $token = $this->get_token($code,$error_msg);
        if(!$token['openid'] || $error_msg){
            $this->_error($error_msg);
        }
        $openid = $token['openid'];
        $session_key = $token['session_key'];
        $expires_in = $token['expires_in'];//秒
        $cur_time = time();
        /**
         * 用户授权了除code外更详细资料
         * !!小程序平台暂无后端用户资料接口
         */
        $nickname = $this->_filter_nickname($params['nickName']);
        $gender = $params['gender'];
        $city = $params['city'];
        $country = $params['country'];
        $province = $params['province'];
        $avatar = $params['avatarUrl'];
        //机密信息 内含 unionID 等
        $encryptedData = $params['encryptedData'];
        $iv = $params['iv'];
        $signature = $params['signature'];
        //logger::alert('wxxcx auth params:');
        //logger::alert($params);
        if($iv && strlen($iv) == 24){
            $aes_key = base64_decode($session_key);
            $aes_iv = base64_decode($iv);
            $aes_cipher=base64_decode($encryptedData);
            $result = $this->_decrypt($aes_cipher,$aes_iv,$aes_key);
            //logger::alert($result);
            if($result){
                $unionid = $result['unionId'];
            }
        }


        /*
         * 会员SDF
         */
        $member_sdf = array(
            'avatar' => $avatar, //头像
            'contact' => array(//联系层信息
                'name' => $nickname, //昵称
                'addr' => $country.$city.$province,
                ),
            'profile' => array(//背景层信息
                'gender' => $gender,
            ),
            'addon' => serialize($auth_data),//信任登录返回数据
            'pam_account' => array(//会员登录账号信息
                'openid' => $openid,
                'unionid'=> $unionid,
                'login_account' => 'wx_'.substr(md5($openid), -5), //微信OPEN账号名
                'login_type' => $this->login_type,//登录类型
                'login_password' => md5($cur_time),//自动密码
                'password_account' => $openid,//用唯一openid
                'createtime' => $cur_time,//账号创建时间
            ),
            'regtime' => $cur_time, //会员注册时间
            'source' => 'api', //注册方式
            'reg_ip' => base_request::get_remote_addr(), //注册来源IP
        );

        //call abstract method
        $member_id = $this->dologin($member_sdf, $error_msg);
        if (!$member_id || $error_msg) {
            $this->_error($error_msg);
        } else {
            $mdl_pam_member = app::get('pam')->model('members');
            $login_type_arr = $mdl_pam_member->getColumn('login_type', array('member_id' => $member_id));

            $res_data = array(
                'member_id'=>$member_id,
                'openid'=>$openid,
                'unionid'=>$unionid,
                'exist_login_type'=>$login_type_arr
            );
            
            foreach($this->_response->get_headers() AS $header){
                if(strtolower($header['name']) == strtolower('Set-wxappstorage')){
                    $res_data['X-WXAPPSTORAGE'][] = $header['value'];
                }
            }
            $this->_success($res_data);
        }
    }

    /**
     * 根据用户授权的code 获得access_token.
     */
    private function get_token($code, &$msg)
    {
        $app_id = $this->getConf('app_id', __CLASS__);
        
        if (
                defined('IS_SAAS_APP') &&
                IS_SAAS_APP &&
                defined('IS_SAAS_ON_APP') &&
                IS_SAAS_ON_APP &&
                defined('SAAS_CLIENT_ID') &&
                SAAS_CLIENT_ID &&
                class_exists('open_sys')
        ) {
            $_client_id = $_SERVER['HTTP_X_REQUESTED_SAAS_CLIENT'] ? $_SERVER['HTTP_X_REQUESTED_SAAS_CLIENT'] : false;
            $_order_id = $_SERVER['HTTP_X_REQUESTED_SAAS_ORDER'] ? $_SERVER['HTTP_X_REQUESTED_SAAS_ORDER'] : false;
            if (
                    !$_client_id ||
                    !$_order_id ||
                    $_client_id != SAAS_CLIENT_ID ||
                    $_order_id != SAAS_ORDER_ID
            ) {
                $this->_error('NOT ALLOW,params = error');
                return false;
            }
            $get_component = vmc::singleton('open_sys')->call_openapi('open', 'server', 'get_wx_component', array('app_id' => $app_id));
            if(!$get_component || $get_component['result'] != 'success' || !$get_component['data']){
                $this->_error('NOT ALLOW,component = error');
                return false;
            }
            
            $component_appid = $get_component['data']['appid'];
            $component_access_token = $get_component['data']['token'];
            
            if(!$component_appid || !$component_access_token){
                $this->_error('NOT ALLOW,component = error');
                return false;
            }
            
            $action_url = "https://api.weixin.qq.com/sns/component/jscode2session?appid=$app_id&js_code=$code&grant_type=authorization_code&component_appid=$component_appid&component_access_token=$component_access_token";
            $http_client = vmc::singleton('base_httpclient');
            $res = $http_client->get($action_url);
            $res = json_decode($res, 1);
            if ($res['errcode'] || !$res['session_key'] || !$res['openid']) {
                $msg = 'access_token获取失败!' . $res['errmsg'];
                return false;
            }

            return $res;
        }
        
        $app_secret = $this->getConf('app_secret', __CLASS__);
        $action_url = "https://api.weixin.qq.com/sns/jscode2session?appid=$app_id&secret=$app_secret&js_code=$code&grant_type=authorization_code";
        $http_client = vmc::singleton('base_httpclient');
        $res = $http_client->get($action_url);
        $res = json_decode($res, 1);
        if ($res['errcode'] || !$res['session_key'] ||!$res['openid']) {
            $msg = 'access_token获取失败!'.$res['errmsg'];

            return false;
        }

        return $res;
    }

    private function _filter_nickname($str)
    {
        if ($str) {
            $tmpStr = json_encode($str);
            $tmpStr2 = preg_replace("#(\\\ud[0-9a-f]{3})#ie", '', $tmpStr);
            $return = json_decode($tmpStr2);
            if (!$return) {
                return $this->_filter_nickname($return);
            }
        } else {
            $return = '微信小程序用户';
        }

        return $return;
    }

    private function _success($data)
    {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode(array(
            'result' => 'success',
            'data' => $data,
        ));
        exit;
    }

    private function _error($data)
    {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode(array(
            'result' => 'error',
            'data' => $data,
        ));
        exit;
    }

    private function _decrypt( $aesCipher, $aesIV,$aesKey)
	{
		try {
			$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			mcrypt_generic_init($module, $aesKey, $aesIV);
			//解密
			$decrypted = mdecrypt_generic($module, $aesCipher);
			mcrypt_generic_deinit($module);
			mcrypt_module_close($module);
		} catch (Exception $e) {
			return false;
		}
		try {
			//去除补位字符
			$result = vmc::singleton('wechat_crypt')->decode($decrypted);
		} catch (Exception $e) {
			return false;
		}
        $result = json_decode($result,true);
        $app_id = $this->getConf('app_id', __CLASS__);
        if(!$result || $result['watermark']['appid'] != $app_id)
        {
            //验证水印
			return false;
        }
		return $result;
	}
}
