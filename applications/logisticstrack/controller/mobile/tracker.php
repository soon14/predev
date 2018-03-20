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


class logisticstrack_ctl_mobile_tracker extends b2c_mfrontpage
{
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->verify_member();
        $this->member = $this->get_current_member();
        $this->title = '物流追踪';
        $this->set_tmpl('logisticstrack');
    }
    public function index($order_id)
    {
        if (!$order_id) {
            $this->splash('error');
        }
        $mdl_order = app::get('b2c')->model('orders');
        if (!$mdl_order->count(array('order_id' => $order_id, 'member_id' => $this->member['member_id']))) {
            $this->splash('error');
        }
        $mdl_delivery = app::get('b2c')->model('delivery');
        $mdl_delivery_items = app::get('b2c')->model('delivery_items');
        $delivery_arr = $mdl_delivery->getColumn('delivery_id', array('order_id' => $order_id), 'createtime DESC');
        $pagedata = array();
        foreach ($delivery_arr as $delivery_id) {
            $result = vmc::singleton('logisticstrack_puller')->pull($delivery_id, $errmsg);
            if ($result) {
                foreach ($result['logi_log'] as &$value) {
                    $value['context'] = strip_tags($value['context']);
                }
                $result['delivery_id'] = $delivery_id;
                $result['delivery_items'] = $mdl_delivery_items->getList('*',array('delivery_id'=>$delivery_id));
                $pagedata[] = array('success' => $result);
            } else {
                $pagedata[] = array('error' => $errmsg);
            }
        }
        if ($this->_request->is_ajax()) {
            //异步加入购物车反馈
            $this->splash('success', '', $pagedata);
        } else {
            $this->pagedata['track_result'] = $pagedata;
            $this->page('mobile/tracker/index.html');
        }
    } //End Function
} //End Class
