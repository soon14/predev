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


class vmcshop_task
{




    public function post_install()
    {
        printf("\033[43;31m");//设置颜色
        vmc::singleton('base_shell_prototype')->output_line();
        echo file_get_contents(ROOT_DIR.'/license.txt')."\n";
        echo "恭喜！安装初始化成功！请使用您刚配置的超级管理员账号登陆后台，开始使用VMCSHOP!\n当您开始使用VMCSHOP那一刻起,意味着您同意遵守以上软件安装使用协议。\n";
        vmc::singleton('base_shell_prototype')->output_line();
        printf("\033[40;32m");//设置颜色
        echo "若需要DEMO数据,可以运行 init_demo 命令.\n";

    }

}
