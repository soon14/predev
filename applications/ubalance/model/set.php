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
class ubalance_mdl_set
{

    public function __construct(&$app)
    {
        $this->app = $app;
    }

    public function getRow($cols = '')
    {
        include($this->app->app_dir . '/setting.php');
        $data = array();
        foreach ($setting as $key => $value) {
            $data[$key] = $this->app->getConf($key);
        }

        return $data;
    }
}