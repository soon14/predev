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


 
interface pam_interface_passport{

    function get_name();
    function get_login_form($auth,$appid,$view,$ext_pagedata=array());
    function login($auth,&$usrdata);
    function loginout($auth,$backurl="index.php");
    function get_data();
    function get_id();
    function get_expired();

}
