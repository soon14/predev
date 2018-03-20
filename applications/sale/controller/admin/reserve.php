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

class sale_ctl_admin_reserve extends desktop_controller
{
    public function index($sale_id = 0)
    {
        $mdl_sale = $this->app->model('sales');
        $sale = $mdl_sale->getRow('*',array('id'=>$sale_id));
        $this->finder('sale_mdl_reserve', array(
            'title' => ('预约列表') ,
            'use_buildin_export' => true,
            'use_buildin_recycle'=>false,
            'base_filter' => array('sale_id' => $sale_id),
            'finder_extra_view' => array(
                array(
                    'extra_pagedata'=> array('sale' => $sale),
                    'app' => 'sale',
                    'view' => '/admin/sale/reserve_info.html'
                )
            ),
        ));
    }
}
