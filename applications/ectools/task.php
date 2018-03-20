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




class ectools_task
{
    /**
     * 安装完成后
     */
    public function post_install()
    {
        logger::info('Initial ectools');
        vmc::singleton('base_initial', 'ectools')->init();
        logger::info('Initial Regions');
        vmc::singleton('ectools_regions_mainland')->install();
        vmc::singleton('ectools_regions_operation')->updateRegionData();
    }//End Function
    /**
     * 更新完成后
     */
    public function post_update( $appinfo ){

    }
}//End Class
