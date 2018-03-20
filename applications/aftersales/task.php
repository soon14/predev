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


class aftersales_task
{
    public function post_install()
    {
    }//End Function
    public function post_update($appinfo)
    {
        if (version_compare($appinfo['version'], '1.2', '=')) {
            //7.3.7版本,迁移image_attach 商品
            $SQL_copy_asrequest_image = "INSERT INTO vmc_aftersales_image_attach(`target_id`,`image_id`,`last_modified`) SELECT target_id,image_id,last_modified FROM vmc_image_image_attach WHERE target_type='asrequest'";
            if (vmc::database()->exec($SQL_copy_asrequest_image)) {
                logger::info('copy image_attach to asrequest_image success!');
            }
        }
        logger::info('update to version'.$appinfo['version']);
    }
}//End Class
