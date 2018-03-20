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


class hybirdapp_vhelper
{
    public function function_SYSTEM_FOOTER_M($params, &$smarty)
    {
        if(!base_mobiledetect::is_hybirdapp()){
            return '';
        }
        $req_obj = $smarty->_request;
        $ctl_name = $req_obj->get_ctl_name();
        $act_name = $req_obj->get_act_name();
        $req_params = $req_obj->get_params();
        $smarty->pagedata['ctl_name'] = $ctl_name;
        $smarty->pagedata['act_name'] = $act_name;
        $smarty->pagedata['act_params'] = $req_params;
        $html = $smarty->fetch('bridge.html','hybirdapp');
        return $html;
    }//End Function

}//End Class
