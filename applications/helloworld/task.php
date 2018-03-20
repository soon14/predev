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


class b2c_task
{
    function install_options(){
        return array();
    }
    /**
     * @param array('dbver'=>'1.1');
     */
    public function post_update($appinfo)
    {
        //更新完成后触发
    }

    public function post_install($options)
    {
        //安装完成后触发
    }

    public function post_uninstall()
    {
        //卸载完成后触发
    }

}
