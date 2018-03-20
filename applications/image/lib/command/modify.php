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



class image_command_modify extends base_shell_prototype
{

    var $command_touch = '刷新图片URL后缀版本号';
    /**
     * 强制刷新图片最新更新时间
     * @param null
     * @return null
     */
    public function command_touch()
    {
        vmc::database()->exec('update vmc_image_image SET last_modified = last_modified + 1');
        logger::info('Refresh  images last_modified OK!');
    }//End Function

}//End Class
