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
class ubalance_ctl_admin_set extends desktop_controller
{

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function balance_set()
    {
        if ($_POST) {
            $this->begin();
            foreach ($_POST as $key => $value) {
                $this->app->setConf($key, $value);
            }
            $this->end(true, '保存成功');
        }

        include($this->app->app_dir . '/setting.php');
        foreach ($setting as $key => $value) {
            if ($value['desc']) {
                $this->pagedata['setting'][$key] = $value;
                $this->pagedata['setting'][$key]['value'] = $this->app->getConf($key);
            }
        }

        $this->page('admin/set/index.html');
    }

}
