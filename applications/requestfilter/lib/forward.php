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
class requestfilter_forward {

    //校验跳转的地址是否是外部地址
    public function exec(&$forward) {
        preg_match("/^(http:\/\/|https:\/\/)?([^\/]+)/i", $forward, $matches);
        if($matches[2] && $matches[2] != $_SERVER['HTTP_HOST']) {
            if(base_mobiledetect::is_mobile()) {
                $forward = app::get('mobile')->router()->gen_url(array(
                    'app' => 'mobile',
                    'ctl' => 'index',
                ));;
            }else{
                $forward = app::get('site')->router()->gen_url(array(
                    'app' => 'site',
                    'ctl' => 'index',
                ));;
            }
        }
        return true;
    }
}

?>