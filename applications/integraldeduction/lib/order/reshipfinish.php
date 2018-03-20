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


class integraldeduction_order_reshipfinish
{
    public function exec(&$delivery_sdf, &$msg = '')
    {
        //TODO
        /**
         * 算法不准确
         */

        $order_id = $delivery_sdf['order_id'];
        $delivery_items = app::get('b2c')->model('delivery_items')->getList('order_item_id,goods_id,product_id,sendnum', array('delivery_id' => $delivery_sdf['delivery_id']));
        $order = app::get('b2c')->model('orders')->dump($order_id);
        $order_score_u = $order['score_u'];
        if ($order_score_u <= 0) {
            return true;
        }
        $order_items_map = utils::array_change_key($delivery_items, 'order_item_id');
        $order_item_ids = array_keys($order_items_map);
        $order_items = app::get('b2c')->model('order_items')->getList('item_id,product_id,buy_price', array('item_id' => $order_item_ids));
        $omath = vmc::singleton('ectools_math');
        $mdl_order_pmt = app::get('b2c')->model('order_pmt');
        $sumsend_price = 0;
        $order_scale_enabled = app::get('integraldeduction')->getConf('order_scale_enabled');

        foreach ($order_items as $item) {
            if ($order_scale_enabled != 'true') {
                if (!$mdl_order_pmt->count(array('order_id' => $order_id, 'pmt_tag' => '积分抵扣', 'product_id' => $item['product_id'], 'pmt_type' => 'goods'))) {
                    continue;
                }
            }
            $sumsend_price += $omath->number_multiple(array(
                $item['buy_price'],
                $order_items_map[$item['item_id']]['sendnum'],
            ));
        }
        $score_g = $omath->number_multiple(array(
            $order_score_u,
            $omath->number_div(array(
                $sumsend_price,
                $order['finally_cart_amount'],
            )),
        ));

        if ($score_g <= 0) {
            return true;
        }
        $integral_charge = array(
            'member_id' => $order['member_id'],
            'change_reason' => 'recharge',//退回抵扣积分
            'order_id' => $order['order_id'],
            'change' => round($score_g),
            'op_model' => 'member',
            'op_id' => $order['member_id'],
            'remark' => '退货积分返还',
        );
        if (!vmc::singleton('b2c_member_integral')->change($integral_charge, $error_msg)) {
            $msg = '积分抵扣退回失败!';
            logger::warning('积分抵扣退回失败.MEMBER_ID:'.$current_member['member_id']);

            return false;
        } else {
            return true;
        }
    }
}
