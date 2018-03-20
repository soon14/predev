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

class vshop_ctl_admin_relorder extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function index()
    {
        $this->finder('vshop_mdl_relorder', array(
            'title' => ('微店订单'),
//            'use_buildin_recycle' => true,
            'use_buildin_set_tag' => true,#是否启用标签
            // 'use_buildin_export' => true, #是否启用批量导出
            // 'use_buildin_import' => true, #是否启用批量导入
            'use_buildin_filter' => true, #是否启用筛选
        ));
    }

    
}
