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


class store_ctl_admin_orders extends store_ctl_admin_controller
{

    public function __construct($app) {
        parent::__construct($app);

    }

    /**
     * 店铺订单列表
     */
    public function index()
    {
        $options = array(
            'title'=>'门店订单列表',
            'finder_extra_view'=>array(
                array(
                    'app'=>'store',
                    'view'=>'/admin/finder/top_store_filter.html',
                    'extra_pagedata' => $this->can_cashier_store_ids,
                ),

            ),
             'use_buildin_export' => true,
             'use_buildin_set_tag' => true,
             'use_buildin_filter' => true
        );

        $this->finder('store_mdl_storeorder', $options);
    }
}
