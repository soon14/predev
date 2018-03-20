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
class o2ocds_finder_orderlog
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function detail_order($orderlog_id)
    {
        $orderlog = $this->app->model('orderlog')->getRow('*', array('orderlog_id' => $orderlog_id));
        $order = app::get('b2c')->model('orders')->getRow('*', array('order_id' => $orderlog['order_id']));
        $orderlog_items = $this->app->model('orderlog_items')->getList('product_id ,product_fund',
            array('orderlog_id' => $orderlog_id));
        $items = utils::array_change_key($orderlog_items, 'product_id');
        $product = app::get('b2c')->model('products')->getList("product_id , bn ,name",
            array('product_id' => array_keys($items)));
        foreach ($product as $k => $v) {
            $product[$k]['product_fund'] = $items[$v['product_id']]['product_fund'];
        }
        $orderlog_achieve = $this->app->model('orderlog_achieve')->getList('member_id ,achieve_fund',
            array('orderlog_id' => $orderlog_id));
        foreach($orderlog_achieve as $k =>$v){
            $orderlog_achieve[$k]['member_id'] = vmc::singleton('b2c_user_object')->get_member_name(null,$v['member_id']);
        }
        $render = $this->app->render();
        $render->pagedata['orderlog'] = $orderlog;
        $render->pagedata['order'] = $order;
        $render->pagedata['orderlog_items'] = $product;
        $render->pagedata['orderlog_achieve'] = $orderlog_achieve;

        return $render->fetch('admin/orderlog/detail.html');
    }
}