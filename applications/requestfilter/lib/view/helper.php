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
class requestfilter_view_helper{
    public function function_csrf_token($params, &$smarty){
        return vmc::singleton('requestfilter_csrf')->get_token();
    }

    public function function_csrf_field($params, &$smarty){
        $token = vmc::singleton('requestfilter_csrf')->get_token();
        return '<input type="hidden" name="_token" value="'.$token.'">';
    }
}