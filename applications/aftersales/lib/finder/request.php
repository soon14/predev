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


class aftersales_finder_request
{
    public function __construct($app)
    {
        $this->app = $app;
    }
    public $detail_request = '货品信息';
    public function detail_request($request_id)
    {
        $mdl_request = app::get('aftersales')->model('request');

        //vmc::dump($this->pagedata['rq_schema']);
        $mdl_order_items = app::get('b2c')->model('order_items');
        $request_detail = $mdl_request->dump($request_id,'*','default_sub');
        $product = $mdl_order_items->getRow('*',array('order_id'=>$request_detail['order_id'],'product_id'=>$request_detail['product']['product_id']));
        $render = $this->app->render();
        
        $render->pagedata['request_detail'] = $request_detail;
        $render->pagedata['product'] = $product;
        $render->pagedata['rq_schema']  = $mdl_request->get_schema();

        return $render->fetch('admin/request/detail.html');
    }

}
