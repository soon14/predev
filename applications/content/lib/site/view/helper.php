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




/**
* 前台头尾部内容类
*/
class content_site_view_helper
{

    function function_SYSTEM_HEADER($params, &$smarty)
    {
        if($smarty->app->app_id !='content') return '';

        return '';

        return $smarty->fetch('site/common/header.html', app::get('content')->app_id);
    }


}//End Class
