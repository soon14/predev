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



#站点
class operatorlog_site{

    function __construct(){
        $this->objlog = vmc::singleton('operatorlog_service_desktop_controller');
    }

    // 记录站点配置日志
    function logSiteConfigInfo($confinName,$pre_config,$now_config){
        $memo .= '配置 ' . $confinName . ' 由 '. $pre_config . ' 修改为 ' . $now_config;
        $this->objlog->logs('site', '站点配置', $memo);
    }

}//End Class
