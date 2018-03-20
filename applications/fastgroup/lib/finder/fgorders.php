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


class fastgroup_finder_fgorders
{
    public $detail_fgorder = '订购详情';


    public function __construct($app)
    {
        $this->app = $app;
    }

    public function detail_fgorder($skey)
    {

        $mdl_fgorders = app::get('fastgroup')->model('fgorders');
        $mdl_orders = app::get('b2c')->model('orders');
        $mdl_subject = app::get('fastgroup')->model('subject');
        $fgorder = $mdl_fgorders->dump($skey);
        $order = $mdl_orders->dump($fgorder['order_id'], '*', array(
            'items' => array(
                '*',
            ),
            // 'promotions' => array(
            //     '*',
            // ),
            // ':dlytype' => array(
            //     '*',
            // ),
        ));
        $subject = $mdl_subject->dump($fgorder['subject_id']);
        $render = $this->app->render();
        $render->pagedata['fgorder'] = $fgorder;
        $render->pagedata['order'] = $order;
        $render->pagedata['subject'] = $subject;
        $render->pagedata['order_status_label'] = vmc::singleton('b2c_finder_orders')->column_orderstatus($order);
        return $render->fetch('admin/fgorders/detail.html');
    }



    public function row_style($row)
    {
        //$row = $row['@row'];
    }
}
