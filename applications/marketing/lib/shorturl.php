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
class marketing_shorturl{

    public function __construct(){
        $this ->http= vmc::singleton('base_httpclient');
    }


    public function get_short($url){
        $api ='http://c7.gg/api.php?url='.$url;
        $res = $this ->http->get($api);
        return $res;
    }
}