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


class openidcheck_openidcheck
{
    public function checklogin($member_id, &$check_redirect)
    {
        if(vmc::singleton('base_component_request')->is_ajax()){
            return false;
        }
        vmc::singleton('base_session')->start();
        if ($_SESSION['openidcheck']['ignore']) {
            //忽略认证
            return false;
        }
        $mdl_pam_member = app::get('pam')->model('members');
        if (method_exists($mdl_pam_member, 'getColumn')) {
            $login_type_arr = $mdl_pam_member->getColumn('login_type', array('member_id' => $member_id));
        } else {
            $login_type = $mdl_pam_member->getList('login_type', array('member_id' => $member_id));
            if ($login_type) {
                foreach ($login_type as $value) {
                    $login_type_arr[] = $value['login_type'];
                }
            } else {
                $login_type_arr = array();
            }
        }

        $check_pam_login_type_arr = array('local','mobile','email');
        if (array_intersect($login_type_arr, $check_pam_login_type_arr)) {
            $_SESSION['openidcheck']['ignore'] = true;

            return false;
        }
        $redirect = app::get('site')->router()->gen_url(array(
            'app' => 'openidcheck',
            'ctl' => 'site_openidcheck',
            'act' => 'index',
        ));

        header('Content-type: text/html; charset=UTF-8');
        echo "<header><meta http-equiv=\"refresh\" content=\"0; url={$redirect}\"></header>";
        exit;
    }
    //会员登出时执行
    /**
     *
     */
    public function logout()
    {
        vmc::singleton('base_session')->start();
        unset($_SESSION['openidcheck']['ignore']);
        unset($_SESSION['openidcheck']);

        return true;
    }
}
