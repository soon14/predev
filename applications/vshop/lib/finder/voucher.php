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


class vshop_finder_voucher
{
    public $detail_voucher = '凭证详情';
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function detail_voucher($voucher_id)
    {
        $render = $this->app->render();
        $mdl_voucher = $this->app->model('voucher');
        $mdl_vshop = $this->app->model('shop');
        $voucher = $mdl_voucher->dump($voucher_id, '*', 'items');
        $vshop = $mdl_vshop->dump($voucher['vshop_id']);
        $render->pagedata['voucher'] = $voucher;
        $render->pagedata['vshop'] = $vshop;
        //发货单据信息
        $mdl_delivery = app::get('b2c')->model('delivery');
        $delivery = $mdl_delivery->dump($voucher['delivery_id'],'*','delivery_items');
        $render->pagedata['delivery'] = $delivery;
        if (!empty($delivery['logistics_no']) && $delivery['logistics_no'] != '') {
            $render->pagedata['logistics_tracker'] = vmc::singleton('logisticstrack_puller')->pull($delivery['delivery_id'], $msg);
        }
        return $render->fetch('admin/voucher/detail.html');
    }

    // public function row_style($row)
    // {
    //     //$row = $row['@row'];
    // }
}
