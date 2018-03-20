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

class requestfilter_service_xss
{
    public function parse_query(&$query){
        if(app::get('requestfilter')->getConf('xss_filter') =='true'){
            $query = utils::_filter_input($query);
        }
    }
}//End Class
