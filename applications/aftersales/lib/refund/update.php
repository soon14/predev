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


class aftersales_refund_update
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 用于订单退货确认，并影响售后服务单.
     *
     * @params array - 退货单据数据SDF
     *
     * @return bool - 执行成功与否
     */
    public function exec($bill_sdf, &$msg = '')
    {
        $mdl_as_request = $this->app->model('request');
        $req_order = $mdl_as_request->getRow('*', array('bill_id' => $bill_sdf['bill_id']));
        if (!$req_order) {
            return true;
        }
        if ($req_order['status'] != '4' || $bill_sdf['status'] != 'succ') {
            $msg = '不在售后退款流程内,或错误的退款单据状态！';
            return false;
        }
        $req_order['status'] = '5'; //退款成功,完成售后流程
        if ($mdl_as_request->save($req_order)) {
            return true;
        } else {
            $msg = '存在售后服务单据,售后服务单据状态更新异常！';
            logger::warning($msg.'request_id:'.$req_order['request_id']);

            return false;
        }
    }
}
