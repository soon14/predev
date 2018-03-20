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
 * 认证统一调用类，该类设置回调地址，认证体系类型
 * (shopadmin,member等).
 */
class pam_auth
{
    /**
     * account实例.
     */
    private $account;
    /**
     * 该类的实例.
     *
     * @var array
     */
    public static $instance = array();
    /**
     *构造方法.
     *
     * @param string $type 体系类型
     */
    public function __construct($type)
    {
        $this->type = $type;
    }
    /**
     * 实例化本类.
     *
     * @return array 返回实例类型
     *
     * @param string $type 体系类型
     */
    public static function instance($type)
    {
        if (!isset(self::$instance[$type])) {
            self::$instance[$type] = new self($type);
        }

        return self::$instance[$type];
    }
    /**
     * 设置app_id.
     *
     * @param string $appid appid
     */
    public function set_appid($appid)
    {
        $this->appid = $appid;
    }
    /**
     * 给account变量赋值.
     *
     * @return object 返回pam_account这个类的对象
     */
    public function account()
    {
        if (!$this->account) {
            $this->account = new pam_account($this->type);
        }

        return $this->account;
    }
    /**
     * 认证方式的名称.
     *
     * @param string $module 认证方式类名
     *
     * @return string 返回认证方式的名称
     */
    public function get_name($module)
    {
        return app::get('pam')->getConf('module.name.'.$module);
    }
    /**
     * 验证认证方式是否开启，可用.
     *
     * @param string $module 认证方式类名
     * @param string $app_id 认证的appid
     *
     * @return bool 返回认证方式的开启状态
     */
    public function is_module_valid($module, $app_id = 'b2c')
    {
        $obj = vmc::singleton($module);
        $config = $obj->get_config();
        $type = $app_id === 'desktop' ? 'shopadmin' : 'site';

        return $config[$type.'_passport_status']['value'] == 'true' ?  true : false;
    }
    /**
     * 获取回调的openapi地址.
     *
     * @param string $module 认证方式类名
     *
     * @return string 返回回调的openapi地址
     */
    public function get_callback_url($module)
    {
        return vmc::openapi_url('openapi.pam_callback', 'login', array('module' => $module, 'type' => $this->type, 'appid' => $this->appid, 'redirect' => $this->redirect_url));
    }
    /**
     * 设置回调的openapi地址.
     *
     * @param string $url 验证完毕后要跳转的地址
     */
    public function set_redirect_url($url)
    {
        $this->redirect_url = $url;
    }
    /**
     * 判断验证码是否开启.
     *
     * @return bool 返回验证码开启状态
     */
    public function is_enable_vcode()
    {
        if (!class_exists($this->appid.'_service_vcode')) {
            return false;
        }
        $vcode = vmc::singleton($this->appid.'_service_vcode');

        return $vcode->status();

        return false;
    }
}
