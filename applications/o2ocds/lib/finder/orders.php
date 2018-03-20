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
class o2ocds_finder_orders
{
    public $column_service_code = '服务码';
    public $column_store_name = '分销店铺';
    public $column_enterprise_name = '分销企业';

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_service_code($row)
    {
        if ($service_code = $this->app->model('service_code')->getRow('service_code', array('order_id' => $row['order_id']))['service_code']) {
            return $service_code;
        };
        return '';
    }

    public function column_store_name ($row) {
        if ($store_id = $this->app->model('service_code')->getRow('store_id', array('order_id' => $row['order_id']))['store_id']) {
            if($name = $this->app->model('store')->getRow('name',array('store_id'=>$store_id))) {
                return $name['name'];
            }
        };
        return '';
    }

    public function column_enterprise_name ($row) {
        if ($enterprise_id = $this->app->model('service_code')->getRow('enterprise_id', array('order_id' => $row['order_id']))['enterprise_id']) {
            if($name = $this->app->model('enterprise')->getRow('name',array('enterprise_id'=>$enterprise_id))) {
                return $name['name'];
            }
        };
        return '';
    }


}