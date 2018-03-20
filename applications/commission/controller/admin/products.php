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
class commission_ctl_admin_products extends desktop_controller
{
    public function index()
    {
        $this->finder('commission_mdl_products_extend', array(
            'title' => ('商品分佣设置'),
            'use_buildin_recycle' => false,
            'finder_extra_view' => array(array('app' => 'commission', 'view' => '/admin/products/finder_extra.html'),),
        ));
    }

    public function update_commission()
    {
        $this->begin('index.php?app=commission&ctl=admin_product&act=index');
        $params = $_POST;
        if (!$params['product_id']) {
            $this->end(false);
        }
        $service_chk_column = vmc::singleton('commission_service_check_column');
        if (false == $service_chk_column->check_column_value($params, $msg)) {
            $this->end(false, $msg);
        }
        $this->app->model('products_extend')->update($params,
            array('product_id' => $params['product_id'])
        );
        $this->end(true);
    }

}
