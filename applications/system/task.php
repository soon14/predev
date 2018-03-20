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



class system_task{

    function post_install()
    {
        logger::info('Don`t forget mv "queue config file" from '.APP_DIR.'/base/examples/queue.php to '.ROOT_DIR.'/config/queue.php');
        // if (system_queue::write_config()){
        //     logger::info('Writing queue config file ... ok.');
        // }else{
        //     trigger_error('Writing queue config file fail, Please check config directory has write permission.', E_USER_ERROR);
        // }
    }//End Function

    function post_update($params){
        logger::info('Don`t forget mv "queue config file" from '.APP_DIR.'/base/examples/queue.php to '.ROOT_DIR.'/config/queue.php');
        // $dbver = $params['dbver'];
        // if (system_queue::write_config()){
        //     logger::info('Writing queue config file ... ok.');
        // }else{
        //     trigger_error('Writing queue config file fail, Please check config directory has write permission.', E_USER_ERROR);
        // }
    }


}
