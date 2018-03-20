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


/**
 * 该类是系统基本的验证类，必须实现 pam_interface_passport 这个接口.
 */
class pam_passport_desktop_basic implements pam_interface_passport
{
    /**
     * 构造方法,初始化配置信息.
     */
    public function __construct()
    {
        vmc::singleton('base_session')->start();
        $this->init();
    }

    /**
     * 获取配置信息.
     *
     * @return array 返回配置信息数组
     */
    public function init()
    {
        if ($ret = app::get('pam')->getConf('passport.'.__CLASS__)) {
            return $ret;
        } else {
            $ret = $this->get_setting();
            $ret['passport_id']['value'] = __CLASS__;
            $ret['passport_name']['value'] = $this->get_name();
            $ret['shopadmin_passport_status']['value'] = 'true';
            $ret['site_passport_status']['value'] = 'true';
            $ret['passport_version']['value'] = '1.5';
            app::get('pam')->setConf('passport.'.__CLASS__, $ret);

            return $ret;
        }
    }
    /**
     * 获取认证方式名称.
     *
     * @return string 返回名称
     */
    public function get_name()
    {
        return '系统默认登录';
    }
    /**
     * 生成认证表单,包括用户名,密码,验证码等input.
     *
     * @param object $auth  pam_auth 对象
     * @param string $appid app_id
     *
     * @return string 返回HTML页面
     */
    public function get_login_form($auth, $appid, $view, $ext_pagedata = array())
    {
        $render = app::get('pam')->render();
        $callback = $auth->get_callback_url(__CLASS__);
        $render->pagedata['callback'] = $callback;
        if ($auth->is_enable_vcode()) {
            $render->pagedata['show_varycode'] = 'true';
            $render->pagedata['type'] = $auth->type;
        }
        if (isset($_SESSION['last_error']) && ($auth->type == $_SESSION['type'])) {
            $render->pagedata['error_info'] = $_SESSION['last_error'];
            unset($_SESSION['last_error']);
            unset($_SESSION['type']);
        }
        if ($ext_pagedata) {
            foreach ($ext_pagedata as $key => $v) {
                $render->pagedata[$key] = $v;
            }
        }

        return $render->fetch($view, $appid);
    }
    /**
     * 认证用户名密码以及验证码等.
     *
     * @param object $auth    pam_auth对象
     * @param array  $usrdata 认证提示信息
     *
     * @return bool|int返回认证成功与否
     */
    public function login($auth, &$usrdata)
    {
        if ($auth->is_enable_vcode()) {
            $key = $auth->appid;
            if (!base_vcode::verify($key, $_POST['verifycode'])) {
                $usrdata['log_data'] = '验证码不正确！';
                $_SESSION['error'] = '验证码不正确！';

                return false;
            }
        }

        $password_string = pam_encrypt::get_encrypted_password($_POST['password'], $auth->type, array('login_name' => $_POST['uname']));
        if (!$_POST['uname'] || !$password_string || ($_POST['password'] !== '0' && !$_POST['password'])) {
            $usrdata['log_data'] = '验证失败！';
            $_SESSION['error'] = '用户名或密码错误';
            $_SESSION['error_count'][$auth->appid] = $_SESSION['error_count'][$auth->appid] + 1;

            return false;
        }

        $rows = app::get('pam')->model('account')->getList('*', array(
        'login_name' => $_POST['uname'],
        'login_password' => $password_string,
        'account_type' => $auth->type,
        'disabled' => 'false',
        ), 0, 1);

        if ($rows[0]) {
            if (isset($_POST['remember'])) {
                setcookie('autologin', 365 * 24 * 60 , time() + 365 * 24 * 3600, '/');
            }
            $usrdata['log_data'] = '用户'.$_POST['uname'].'验证成功！';
            unset($_SESSION['error_count'][$auth->appid]);
            if (substr($rows[0]['login_password'], 0, 1) !== 's') {
                $pam_filter = array(
                            'account_id' => $rows[0]['account_id'],
                            );
                $string_pass = md5($rows[0]['login_password'].$rows[0]['login_name'].$rows[0]['createtime']);
                $update_data['login_password'] = 's'.substr($string_pass, 0, 31);
                app::get('pam')->model('account')->update($update_data, $pam_filter);
            }
            //用户登录有效
            base_kvstore::instance('cache/member/login')->store($rows[0]['account_id'] ,1,3600*24*365);
            return $rows[0]['account_id'];
        } else {
            $usrdata['log_data'] = '用户'.$_POST['uname'].'验证失败！';
            $_SESSION['error'] = '用户名或密码错误';
            $_SESSION['error_count'][$auth->appid] = $_SESSION['error_count'][$auth->appid] + 1;

            return false;
        }
    }
    /**
     * 退出相关操作.
     *
     * @param object $autn    pam_auth对象
     * @param string $backurl 跳转地址
     */
    public function loginout($auth, $backurl = 'index.php')
    {
        unset($_SESSION['account'][$auth->type]);
        unset($_SESSION['account'][$auth->type.'_data']);
        unset($_SESSION['error']);
        unset($_SESSION['error_count']);
    }

    public function get_data()
    {
    }

    public function get_id()
    {
    }

    public function get_expired()
    {
    }

    /**
     * 得到配置信息.
     *
     * @return array 配置信息数组
     */
    public function get_config()
    {
        $ret = app::get('pam')->getConf('passport.'.__CLASS__);
        if ($ret && isset($ret['shopadmin_passport_status']['value']) && isset($ret['site_passport_status']['value'])) {
            return $ret;
        } else {
            $ret = $this->get_setting();
            $ret['passport_id']['value'] = __CLASS__;
            $ret['passport_name']['value'] = $this->get_name();
            $ret['shopadmin_passport_status']['value'] = 'true';
            $ret['site_passport_status']['value'] = 'true';
            $ret['passport_version']['value'] = '1.5';
            app::get('pam')->setConf('passport.'.__CLASS__, $ret);

            return $ret;
        }
    }
    /**
     * 设置配置信息.
     *
     * @param array $config 配置信息数组
     *
     * @return bool 配置信息设置成功与否
     */
    public function set_config(&$config)
    {
        $save = app::get('pam')->getConf('passport.'.__CLASS__);
        if (count($config)) {
            foreach ($config as $key => $value) {
                if (!in_array($key, array_keys($save))) {
                    continue;
                }
                $save[$key]['value'] = $value;
            }
        }
        $save['shopadmin_passport_status']['value'] = 'true';

        return app::get('pam')->setConf('passport.'.__CLASS__, $save);
    }
    /**
     * 获取finder上编辑时显示的表单信息.
     *
     * @return array 配置信息需要填入的项
     */
    public function get_setting()
    {
        return array(
            'passport_id' => array('label' => '通行证id','type' => 'text','editable' => false),
            'passport_name' => array('label' => '通行证','type' => 'text','editable' => false),
            'shopadmin_passport_status' => array('label' => '后台开启','type' => 'bool','editable' => false),
            'site_passport_status' => array('label' => '前台开启','type' => 'bool'),
            'passport_version' => array('label' => '版本','type' => 'text','editable' => false),
        );
    }
}
