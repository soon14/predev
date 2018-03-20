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

class routeplus_service_statics
{
    public function parse_query(&$query){
        $val = vmc::singleton('routeplus_rstatics')->get_dispatch($query);
        if($val){
            if($val['enable'] == 'true'){
                $query = $val['url'];
            }
        }
    }

    public function parse_url(&$url){
        $val = vmc::singleton('routeplus_rstatics')->get_genurl($url);
        if($val){
            $url = $val;
        }
    }
}//End Class
