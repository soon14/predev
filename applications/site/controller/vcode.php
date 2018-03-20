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



class site_ctl_vcode{
    function index($key='passport'){
        $vcode = vmc::singleton('base_vcode');
        $vcode->length(4);
        $vcode->verify_key($key);
        $vcode->display();
    }
}
