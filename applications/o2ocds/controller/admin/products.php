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
class o2ocds_ctl_admin_products extends desktop_controller
{
    public function index()
    {
        $this->finder('o2ocds_mdl_products_extend', array(
            'title' => ('商品分佣设置'),
            'actions' => array(array(
                'label' => ('同步商品数据') ,
                'icon' => 'fa-save',
                'data-submit-result' => 'index.php?app=o2ocds&ctl=admin_products&act=save_products',
            )),
            'use_buildin_recycle' => false,
            'finder_extra_view' => array(array('app' => 'o2ocds', 'view' => '/admin/products/finder_extra.html'),),
            'use_buildin_export' => true,
            'use_buildin_import' => true,
        ));
    }

    public function update_o2ocds()
    {
        $this->begin('index.php?app=o2ocds&ctl=admin_product&act=index');
        $params = $_POST;
        if (!$params['product_id']) {
            $this->end(false);
        }
        $service_chk_column = vmc::singleton('o2ocds_service_check_column');
        if (false == $service_chk_column->check_column_value($params, $msg)) {
            $this->end(false, $msg);
        }
        $this->app->model('products_extend')->update($params,
            array('product_id' => $params['product_id'])
        );
        $this->end(true);
    }

    public function save_products() {
        $this->begin('index.php?app=o2ocds&ctl=admin_product&act=index');
        $service_service_products = vmc::singleton('o2ocds_service_products');
        if(method_exists($service_service_products,'exec')) {
            if(!$service_service_products->exec('',$msg)) {
                $this->end(false,$msg);
            };
        }
        $this->end(true,'操作成功');
    }
}
