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
class o2ocds_ctl_admin_relation extends desktop_controller
{

    public function __construct(&$app)
    {
        parent::__construct($app);
    }

    public function index() {

        $this->finder('o2ocds_mdl_relation',array(
            'title' => ('关系列表'),
            //'use_buildin_recycle' => true,
            'use_buildin_filter' => true,
        ));
    }

}