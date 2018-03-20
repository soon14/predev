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
class store_finder_income_receipts{
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function detail_income_receipts($id){
        $bn =$this->app->model('income_receipts') ->getRow('income_receipts_bn' ,array('income_receipts_id' => $id));
        $render = $this->app->render();
        $render->pagedata['items'] =$this->app->model('income_receipts_item') ->getList("*" ,array('income_receipts_bn' =>$bn));
        return $render->fetch('admin/stock/income_items.html');
    }
}