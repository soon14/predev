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
class o2ocds_ctl_admin_service_code extends desktop_controller
{

    public function index()
    {
        $this->finder('o2ocds_mdl_service_code', array(
            'title' => ('服务码列表'),
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
        ));
    }

    /*
     * 配置服务码
     * */
    public function setting() {
        if($_POST) {
            $this->begin();
            foreach ($_POST as $key => $value) {
                $this->app->setConf($key, $value);
            }
            $this->end(true, '保存成功');
        }
        $this->pagedata['servicecode_ratio'] = $this->app->getConf('servicecode_ratio');

        $this->page('admin/service/setting.html');
    }


}