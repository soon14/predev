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
 * 登录统一调用的类，该类执行验证已经验证后的跳转.
 */
class pam_callback extends base_openapi
{
    /**
     * 登录调用的方法.
     *
     * @param array $params 认证传递的参数,包含认证类型，跳转地址等
     */
    public function login($params)
    {
        $params['module'] = utils::_filter_input($params['module']);//过滤xss攻击
        $auth = pam_auth::instance($params['type']);
        $auth->set_appid($params['appid']);
        if (!class_exists($params['module'])) {
            vmc::singleton('site_router')->http_status(500);
        }
        if ($params['module']) {
            if (class_exists($params['module']) && ($passport_module = vmc::singleton($params['module']))) {
                if ($passport_module instanceof pam_interface_passport) {
                    $module_uid = $passport_module->login($auth, $auth_data);
                    if ($module_uid) {
                        $auth_data['account_type'] = $params['type'];
                        $auth->account()->update($params['module'], $module_uid, $auth_data);
                    }
                    $log = array(
                        'event_time' => time(),
                        'event_type' => $auth->type,
                        'event_data' => base_request::get_remote_addr().':'.$auth_data['log_data'].':'.$_SERVER['HTTP_REFERER'],

                    );
                    app::get('pam')->model('log_desktop')->insert($log);
                    if (!$module_uid) {
                        $_SESSION['last_error'] = $auth_data['log_data'];
                    }
                    $_SESSION['type'] = $auth->type;
                    $_SESSION['login_time'] = time();
                    $params['member_id'] = $_SESSION['account'][$params['type']];
                    $params['uname'] = $_POST['uname'];
                    foreach (vmc::servicelist('pam_login_listener') as $service) {
                        $service->listener_login($params);
                    }
                    if ($params['redirect'] && $module_uid) {
                        $service = vmc::service('callback_infomation');
                        if (is_object($service)) {
                            if (method_exists($service, 'get_callback_infomation') && $module_uid) {
                                $data = $service->get_callback_infomation($module_uid, $params['type']);
                                if (!$data) {
                                    $url = '';
                                } else {
                                    $url = '?'.utils::http_build_query($data);
                                }
                            }
                        }
                    }

                    if ($_COOKIE['autologin'] > 0) {
                        vmc::singleton('base_session')->set_cookie_expires($_COOKIE['autologin']);
                        //如果自动登录，设置cookie过期时间，单位：分
                    }
                    if ($_COOKIE['S']['SIGN']['AUTO'] > 0) {
                        $minutes = 10 * 24 * 60;
                        vmc::singleton('base_session')->set_cookie_expires($minutes);
                    }

                    if ($_SESSION['callback'] && !$module_uid) {
                        $callback_url = $_SESSION['callback'];
                        unset($_SESSION['callback']);
                        header('Location:'.urldecode($callback_url));
                        exit;
                    } else {
                        $url = base64_decode(str_replace('%2F', '/', urldecode($params['redirect']))).$url;
                        if (!$url) {
                            foreach (vmc::$url_app_map as $key => $value) {
                                $app = current($value);
                                if ($app == 'desktop') {
                                    $url = $key;
                                }
                            }
                        }
                        header('Location: '.$url);
                        exit;
                    }
                }
            } else {
            }
        }
    }

    public function __destruct()
    {
        #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        if ($obj_operatorlogs = vmc::service('operatorlog.system')) {
            if (method_exists($obj_operatorlogs, 'logAdminLoginInfo')) {
                $obj_operatorlogs->logAdminLoginInfo($this);
            }
        }
        #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    }
}
