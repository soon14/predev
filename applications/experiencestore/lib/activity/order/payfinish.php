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


class experiencestore_activity_order_payfinish
{
    /**
     * 订单支付后的处理.
     *
     * @params array 支付完的信息
     * @params 支付时候成功的信息
     */
    public function exec(&$bill, &$msg = '')
    {
        //保留原来的交易单信息，从组数据可能修改过原有的信息
        $prototype_bill = $bill;
        $order_id = $bill['order_id'];
        $mdl_order = app::get('experiencestore')->model('activity_order');
        $order_sdf = $mdl_order->dump($order_id);

        if (empty($order_sdf)) {
            $msg = '未知订单ID';

            return false;
        }
        logger::debug('ACTIVITY_SCHEDULE_ORDER:'.$bill['order_id'].'payfinish exec');

        if ($bill['status'] != 'succ' && $bill['status'] != 'progress') {
            $msg = '支付其实没有完成!';

            return false;
        }
        $omath = vmc::singleton('ectools_math');
        $payed = $omath->number_plus(array(
            $bill['money'],
            $order_sdf['payed'],
        ));
        if (!$mdl_order->update(array('payed' => $payed, 'payed_time' => time()), array('id' => $order_id))) {
            $msg = '订单主单据信息更新失败!ACTIVITY_SCHEDULE_ORDER:'.$order_id;
            return false;
        }

        return true;
    }
}
