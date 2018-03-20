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

class vmcconnect_object_api_input_jd_order extends vmcconnect_object_api_input_jd_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->pack_name = 'order';
    }

    // order.read.getbyId - 获取单个订单 
    public function read_getbyId() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // order.read.search - 订单检索 
    public function read_search() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // order.read.notPayOrderInfo - 批量查询未付款订单 
    public function read_notPayOrderInfo() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // order.read.notPayOrderById - 未付款订单单条记录查询 
    public function read_notPayOrderById() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // order.read.remarkByOrderId - 查询商家备注 
    public function read_remarkByOrderId() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // order.write.remarkUpdate - 商家订单备注修改 
    public function write_remarkUpdate() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // order.bill.write.pay - 订单付款 
    public function bill_write_pay() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // order.bill.write.refund - 订单退款 
    public function bill_write_refund() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // order.delivery.write.send - 订单发货 
    public function delivery_write_send() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // order.delivery.write.reship - 订单退货 
    public function delivery_write_reship() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // order.write.cancel - 订单作废 
    public function write_cancel() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // order.write.end - 订单归档完成 
    public function write_end() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

}
