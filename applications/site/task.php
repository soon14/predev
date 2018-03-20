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


class site_task
{
    public function post_install()
    {
        logger::info('Initial themes');
        $themes = vmc::singleton('site_theme_install')->check();
    }//End Function

    public function post_update($params)
    {
        vmc::singleton('site_module_base')->create_site_config();
        //缓存全部更新, 改造了缓存机制
        cachemgr::clean($msg);
    }
}//End Class
