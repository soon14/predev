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
 * 该类是所有登录方式验证后写日志和SESSION的类.
 */
class pam_account
{
    /**
     * 构造方法.
     *
     * @param string $type 设置登录体系类型 shopadmin,member等
     */
    public function __construct($type)
    {
        $this->type = $type;
        $this->session = vmc::singleton('base_session')->start();
    }
    /**
     * 检查session id 是否存在.
     *
     * @return int 当前用户的id
     */
    public function is_valid()
    {
        return $_SESSION['account'][$this->type];
    }
    /**
     * 检查用户名是否存在(暂时后台).
     *
     * @param string $login_name 用户名字段
     *
     * @return bool 返回存在与否
     */
    public function is_exists($login_name)
    {
        if (app::get('pam')->model('account')->getList('account_id', array('account_type' => $this->type, 'login_name' => $login_name))) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 验证完成后,写入session和记录登录日志.
     *
     * @param string $module     认证模型
     * @param int    $module_uid 用户的id account表里的主键
     * @param array  $auth_data  认证提示信息
     *
     * @return bool
     */
    public function update($module, $module_uid, $auth_data)
    {
        if ($module != 'pam_passport_desktop_basic') {
            $auth_model = app::get('pam')->model('auth');
            if ($row = $auth_model->getlist('*', array(
                    'module_uid' => $module_uid,
                    'module' => $module,
                ), 0, 1)) {
                $auth_model->update(array('data' => $auth_data), array(
                    'module_uid' => $module_uid,
                    'module' => $module,
                ));
                $account_id = $row[0]['account_id'];
            } else {
                $account = app::get('pam')->model('account');
                $login_name = microtime();
                while ($row = $account->getList('account_id', array('login_name' => $login_name, 'account_type' => $this->type))) {
                    $login_name = microtime();
                }
                $data = array(
                            'login_name' => $login_name,
                            'login_password' => md5(time()),
                            'account_type' => $this->type,
                            'createtime' => time(),
                    );
                $account_id = $account->insert($data);
                if (!$account_id) {
                    return false;
                }
                $data = array(
                    'account_id' => $account_id,
                    'module_uid' => $auth_data['login_name'],
                    'module' => $module,
                    'data' => $auth_data,
                );
                $auth_model->insert($data);
            }
        } else {
            $account_id = $module_uid;
        }

        $_SESSION['account'][$this->type] = $account_id;

        return true;
    }
    /**
     * 安装时，注册体系类型.
     *
     * @param string $app_id appid
     * @param string $type   传入的体系的值
     * @param string $name   体系名称
     */
    public static function register_account_type($app_id, $type, $name)
    {
        $account_types = app::get('pam')->getConf('account_type');
        $account_types[$app_id] = array('name' => $name, 'type' => $type);
        app::get('pam')->setConf('account_type', $account_types);
    }
    /**
     * 注销体系类型.
     *
     * @param string $app_id appid
     */
    public static function unregister_account_type($app_id)
    {
        $account_types = app::get('pam')->getConf('account_type');
        unset($account_types[$app_id]);
        app::get('pam')->setConf('account_type', $account_types);
    }
    /**
     * 返回体系类型.
     *
     * @param string $app_id appid
     *
     * @return string 返回体系字符串
     */
    public static function get_account_type($app_id = 'b2c')
    {
        $aType = app::get('pam')->getConf('account_type');
        //todo
        return $aType[$app_id]['type'];
        //return 'member';
    }//End Function
}
