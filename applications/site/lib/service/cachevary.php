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


 

class site_service_cachevary 
{
    public function get_varys() 
    {
        $varys['SEPARATOR'] = trim(app::get('site')->getConf('base_site_params_separator'));
        $varys['URI_EXPENEDE_NAME'] = (app::get('site')->getConf('base_enable_site_uri_expanded') == 'true') ? '.' . app::get('site')->getConf('base_site_uri_expanded_name') : '';
        return $varys;
    }//End Function

}//End Class