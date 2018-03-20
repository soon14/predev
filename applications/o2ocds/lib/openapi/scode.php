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


class o2ocds_openapi_scode extends base_openapi
{
    public function __construct()
    {
        $this->req_params = vmc::singleton('base_component_request')->get_params(true);
    }

    public function get_scode() {
        $user_obj = vmc::singleton('b2c_user_object');
        if (!$member_id = $user_obj->get_member_id()) {
            $this->failure('未知会员');
        }
        if(!$order_id = $this->req_params['order_id']) {
            $this->failure('未知订单');
        }
        if(!app::get('b2c')->model('orders')->getRow('order_id',array('order_id'=>$order_id,'member_id'=>$member_id))) {
            $this->failure('未知订单会员');
        };
        if(!$service_code = app::get('o2ocds')->model('service_code')->getRow('*',array('order_id'=>$order_id))) {
            $this->failure('未知服务码');
        };
        $this->success($service_code);
    }

}