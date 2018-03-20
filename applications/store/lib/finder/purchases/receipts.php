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
class store_finder_purchases_receipts{
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function detail_purchases_receipts($id){
        $bn =$this->app->model('purchases_receipts') ->getRow('purchases_receipts_bn' ,array('purchases_receipts_id' => $id));
        $render = $this->app->render();
        $render->pagedata['items'] =$this->app->model('purchases_receipts_item') ->getList("*" ,array('purchases_receipts_bn' =>$bn));
        return $render->fetch('admin/stock/purchases_items.html');
    }
}