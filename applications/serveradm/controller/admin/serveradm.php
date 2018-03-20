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


class serveradm_ctl_admin_serveradm extends desktop_controller
{


    public function index()
    {
        $oStatus = vmc::singleton("serveradm_status");
        $this->pagedata['cache'] =  $oStatus->getCacheInfo();
        $this->pagedata['kvstore'] =  $oStatus->getKVStorageInfo();
        $this->pagedata['db'] =  $oStatus->getMysqlStatus();
        $this->pagedata['xhprof'] =  $oStatus->getXHProfStatus();
        $this->pagedata['server'] =  $oStatus->getServerInfo();
        $this->page('admin/index.html');
    }
}
