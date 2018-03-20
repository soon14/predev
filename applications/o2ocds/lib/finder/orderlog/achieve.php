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
class o2ocds_finder_orderlog_achieve
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function detail_order($achieve_id)
    {
        $render = $this->app->render();
        $orderlog_achieve = $this->app->model('orderlog_achieve')->getRow('*',array('achieve_id'=>$achieve_id));
        $orderlog_id = $orderlog_achieve['orderlog_id'];
        $orderlog = $this->app->model('orderlog')->getRow('*', array('orderlog_id' => $orderlog_id));
        $orderlog_items = $this->app->model('orderlog_items')->getList('*', array('orderlog_id' => $orderlog_id));
        $render->pagedata['orderlog'] = $orderlog;
        $render->pagedata['orderlog_items'] = $orderlog_items;
        $render->pagedata['orderlog_achieve'] = $orderlog_achieve;
        //查询分佣者的信息
        if($orderlog_achieve['type']) {
            $o2ocds_info = $this->app->model($orderlog_achieve['type'])->getRow('*',array($orderlog_achieve['type'].'_id'=>$orderlog_achieve['relation_id']));
            $render->pagedata['o2ocds_info'] = $o2ocds_info;
        }
        //发货单据信息
        $mdl_delivery = app::get('b2c')->model('delivery');
        $delivery_id = $mdl_delivery->getRow('delivery_id',array('order_id'=>$orderlog['order_id']))['delivery_id'];
        $delivery = $mdl_delivery->dump($delivery_id,'*','delivery_items');
        $render->pagedata['delivery'] = $delivery;
        if (!empty($delivery['logistics_no']) && $delivery['logistics_no'] != '') {
            $render->pagedata['logistics_tracker'] = vmc::singleton('logisticstrack_puller')->pull($delivery['delivery_id'], $msg);
        }
        return $render->fetch('admin/orderlog/achieve/detail.html');
    }

    public function row_style(&$row)
    {

    }
}