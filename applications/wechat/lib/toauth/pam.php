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


final class wechat_toauth_pam extends toauth_abstract implements toauth_interface
{
    /**
     * login_type 可选值
     *     'local' => '用户名',
     *     'mobile' => '手机号码',
     *     'email' => '邮箱',
     *     'wechat'=>'微信',
     *     'qq'=>'QQ',
     *     'weibo'=>'新浪微博',
     *     'taobao'=>'淘宝',
     *     'baidu'=>'百度',
     *     'alipay'=>'支付宝',
     *     '163'=>'网易',
     *     'renren'=>'人人',
     *     'sohu'=>'搜狐',
     *     'douban'=>'豆瓣',
     *     'kaixin'=>'开心网',
     *     '360'=>'开心网',
     *     'toauth01'=>'合作账户01',
     *     'toauth02'=>'合作账户02',
     *     'toauth03'=>'合作账户03',
     *     'toauth04'=>'合作账户04',
     *     'toauth04'=>'合作账户05'.
     */
    public $login_type = 'wechat';

    public $name = '微信信任登录';
    public $version = '';

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
            'display_name' => array(
                'title' => '信任登录名称' ,
                'type' => 'text',
                'default' => '微信信任登录',
            ) ,
            'order_num' => array(
                'title' => '排序' ,
                'type' => 'number',
                'default' => 0,
            ) ,
            /*个性化字段开始*/
            'app_id' => array(
                'title' => 'APPID' ,
                'type' => 'text',
            ) ,
            'app_secret' => array(
                'title' => 'APPSECRET' ,
                'type' => 'text',
            ) ,
            'redirect_uri'=>array(
                'title'=>'redirect_uri(回调地址)',
                'type'=>'textarea',
                'default'=>$this->callback_url
            ),
            /*个性化字段结束*/
            'status' => array(
                'title' => '是否开启' ,
                'type' => 'radio',
                'options' => array(
                    'true' => '是' ,
                    'false' => '否' ,
                ) ,
                'default' => 'true',
            ),

        );
    }

    public function authorize_url()
    {
        $app_id = $this->getConf('app_id', __CLASS__);
        $app_secret = $this->getConf('app_secret', __CLASS__);
        $redirect_uri = $this->callback_url;
        //state=STATE 在前台会被跟踪替换成state=$forward;

        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$app_id&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=STATE";
        //微信里
        if (base_mobiledetect::is_wechat()) {
            return $url.'#wechat_redirect';
        }
        //手机中，微信外
        if (base_mobiledetect::is_mobile()) {
            if(base_mobiledetect::is_hybirdapp()){
                return $url;
            }
            return false;
        }
        //适合扫码登录
        return app::get('site')->router()->gen_url(array('app' => 'wechat',
        'ctl' => 'site_wxqrlogin',
        'act' => 'index',
        'args' => array(
            utils::encrypt(array(
                'app_id' => $app_id,
                'app_secret' => $app_secret,
            )),
        ), ));
    }
    /**
     * 同步跳转处理.
     *
     * @see /applications/toauth/lib/api.php
     * @params array - 所有第三方回调参数，包括POST和GET
     */
    public function callback(&$params)
    {
        $code = $params['code'];
        $forward = $params['state']; //最终转向目标
        $access_token = $params['access_token'];
        $openid = $params['openid'];
        if($access_token && $openid){
            $token = array(
                'access_token'=>$access_token,
                'openid'=>$openid
            );
        }else{
            //获得token
            $token = $this->get_token($code, $error_msg);
            if ($error_msg) {
                die($error_msg);
            }
        }
        //获得微信用户open资料
        $userinfo = $this->get_userinfo($token['access_token'], $token['openid'], $error_msg);
        if ($error_msg) {
            header('Content-type: text/html; charset=utf-8');
            die($error_msg);
        }
        $cur_time = time();
        //微信昵称 emoji
        $userinfo['nickname'] = $this->_filter_nickname($userinfo['nickname']);
        $userinfo = utils::_filter_input($userinfo);
        /*
         * 会员SDF
         */
        $member_sdf = array(
            'avatar' => $userinfo['headimgurl'], //头像
            'contact' => array(//联系层信息
                'name' => $userinfo['nickname'], //昵称
                'addr' => $userinfo['country'].$userinfo['city'].$userinfo['province'],
                ),
            'profile' => array(//背景层信息
                'gender' => $userinfo['sex'] == '1' ? '1' : '0',
            ),
            'addon' => serialize($userinfo),//信任登录返回数据
            'pam_account' => array(//会员登录账号信息
                'openid'=>$userinfo['openid'],
                'unionid'=>$userinfo['unionid'],
                'login_account' => 'wx_'.substr(md5($userinfo['openid']),-5), //微信OPEN账号名
                'login_type' => $this->login_type,//登录类型
                'login_password' => md5($cur_time),//自动密码
                'password_account' => $userinfo['openid'],//用唯一openid
                'createtime' => $cur_time//账号创建时间
            ),
            'regtime' => $cur_time, //会员注册时间
            'source' => 'api', //注册方式
            'reg_ip' => base_request::get_remote_addr(), //注册来源IP
        );
        //call abstract method
        $member_id = $this->dologin($member_sdf, $error_msg);
        if ($member_id) {
            $app = base_mobiledetect::is_mobile() ? 'mobile' : 'site';
            if (!$forward) {
                $forward = app::get($app)->router()->gen_url(array(
                    'app' => $app,
                    'ctl' => 'index',
                    'full' => 1,
                ));
            }
            if($params['qrlp']){
                $forward.='?mid='.$member_id.'&enc_str='.$params['qrlp'];
            }

            if($_COOKIE['vmc-hybirdapp-inpresent']){
                $forward = app::get($app)->router()->gen_url(array(
                    'app' => 'hybirdapp',
                    'ctl' => 'mobile_bridge',
                    'act' => 'dispresent',
                ));
                $forward.="?loginsuccess=1";
            }
            header('Location: '.$forward);
        } else {
            header('Content-type: text/html; charset=utf-8');
            die($error_msg);
        }
    }

    /**
     * 根据用户授权的code 获得access_token.
     */
    private function get_token($code, &$msg)
    {
        $app_id = $this->getConf('app_id', __CLASS__);
        $app_secret = $this->getConf('app_secret', __CLASS__);
        $action_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$app_id&secret=$app_secret&code=$code&grant_type=authorization_code";
        $http_client = vmc::singleton('base_httpclient');
        $res = $http_client->get($action_url);
        $res = json_decode($res, 1);
        if ($res['errcode'] || !$res['access_token']) {
            $msg = 'access_token获取失败!'.$res['errmsg'];

            return false;
        }

        return $res;
    }
    /**
     * 根据access_token 或 openid 获得用户资料.
     */
    private function get_userinfo($token, $openid, &$msg)
    {
        $action_url = "https://api.weixin.qq.com/sns/userinfo?access_token=$token&openid=$openid&lang=zh_CN";
        $http_client = vmc::singleton('base_httpclient');
        $res = $http_client->get($action_url);
        $res = json_decode($res, 1);
        if ($res['errcode'] || !$res['nickname']) {
            $msg = '用户信息获得失败!'.$res['errmsg'];

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
            $return = '微信用户';
        }

        return $return;
    }


}
