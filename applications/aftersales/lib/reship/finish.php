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


class aftersales_reship_finish
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
    public function exec($delivery_sdf, &$msg = '')
    {
        if($delivery_sdf['status']!='succ'){
            return true; //退货单未确认,忽略以下执行
        }
        $mdl_as_request = $this->app->model('request');
        $req_order = $mdl_as_request->getRow('*', array('delivery_id' => $delivery_sdf['delivery_id']));
        if (!$req_order) {
            return true;
        }
        if ($req_order['status'] != '3' || $delivery_sdf['status'] != 'succ') {
            $msg = '不在退货处理流程中或错误的退货确认指令！';

            return false;
        }
        $req_order['status'] = '4'; //退货成功,进入退款流程
        if ($mdl_as_request->save($req_order)) {
            return true;
        } else {
            $msg = '存在售后服务单据,售后服务单据状态更新异常！';
            logger::warning($msg.'request_id:'.$req_order['request_id']);

            return false;
        }
    }
}
