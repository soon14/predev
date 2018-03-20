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

class vmcconnect_object_api_input_def_refundapply extends vmcconnect_object_api_input_def_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->pack_name = 'refundapply';
    }

    // refundapply.read.queryPageList - 退款审核单列表查询 
    public function read_queryPageList() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // refundapply.read.queryById - 根据Id查询退款审核单 
    public function read_queryById() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // refundapply.read.getWaitRefundNum - 待处理退款单数查询 
    public function read_getWaitRefundNum() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

    // refundapply.write.replyRefund - 审核退款单 
    public function write_replyRefund() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

}
