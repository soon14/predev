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


class vmcocean_order_pay
{
    public function __construct($app)
    {
        $this->app = $app;
    }


    public function exec($bill_sdf, &$msg = '')
    {
        $order_id = $bill_sdf['order_id'];
        $order_sub_sdf = array(
            'items' => array(
                '*',
            ),
        );
        $order_sdf = app::get('b2c')->model('orders')->dump($order_id, '*', $order_sub_sdf);
        $sa_stage = vmc::singleton('vmcocean_stage');
        $payed_order_data = array(
            '$ip'=>$order_sdf['ip'],
            'OrderId'=>$order_sdf['order_id'],
            'OrderTotal'=>(float)$order_sdf['order_total'],
            'OrderCostFreight'=>(float)$order_sdf['cost_freight'],
            'OrderCostPayment'=>(float)$order_sdf['cost_payment'],
            'OrderCostTax'=>(float)$order_sdf['cost_tax'],
            'OrderPmtMemberLv'=>(float)$order_sdf['memberlv_discount'],
            'OrderPmtProduct'=>(float)$order_sdf['pmt_goods'],
            'OrderPmtOrder'=>(float)$order_sdf['pmt_order'],
            'OrderWeightG'=>(float)$order_sdf['weight'],
            'OrderProductQuantity'=>(int)$order_sdf['quantity'],
            'OrderNeedInvoice'=>($order_sdf['need_invoice']=="true"),
            'OrderScoreU'=>(int)$order_sdf['score_u'],
            'OrderIsCod'=>($order_sdf['is_cod']=='Y'),
            'OrderNeedShipping'=>($order_sdf['need_shipping']=='Y'),
            'PaymentMethod'=>$bill_sdf['pay_app_id'],
            'PaymentOutTradeNo'=>$bill_sdf['out_trade_no'],
            'PaymentAmount'=>(float)$bill_sdf['money'],
            'UTM_SOURCE' => $_COOKIE['UTM_SOURCE'] ? urldecode($_COOKIE['UTM_SOURCE']) : '',
            'UTM_MEDIUM' => $_COOKIE['UTM_MEDIUM'] ? urldecode($_COOKIE['UTM_MEDIUM']) : '',
            'UTM_TERM' => $_COOKIE['UTM_TERM'] ? urldecode($_COOKIE['UTM_TERM']) : '',
            'UTM_CONTENT' => $_COOKIE['UTM_CONTENT'] ? urldecode($_COOKIE['UTM_CONTENT']) : '',
            'UTM_CAMPAIGN' => $_COOKIE['UTM_CAMPAIGN'] ? urldecode($_COOKIE['UTM_CAMPAIGN']) : '',
        );
        $sa_stage->track_event($order_sdf['member_id'],'PayedOrder',$payed_order_data);

        foreach ($order_sdf['items'] as $key => $item) {
            $payed_order_item = array(
                '$ip'=>$order_sdf['ip'],
                'OrderId'=>$order_sdf['order_id'],
                'ProductName'=>$item['name'],
                'ProductSKU'=>$item['bn'],
                'ProductBarcode'=>$item['barcode'],
                'ProductSpec'=>$item['spec_info'],
                'ProductDBGId'=>$item['goods_id'],
                'ProductDBPId'=>$item['product_id'],
                'ProductBuyPrice'=>(float)$item['buy_price'],
                'ProductNums'=>(int)$item['nums'],
                'ProductWeightG'=>(float)$item['weight'],
                'ProductAmount'=>(float)$item['amount'],
                'PaymentMethod'=>$bill_sdf['pay_app_id'],
                'PaymentOutTradeNo'=>$bill_sdf['out_trade_no'],
                'UTM_SOURCE' => $_COOKIE['UTM_SOURCE'] ? urldecode($_COOKIE['UTM_SOURCE']) : '',
                'UTM_MEDIUM' => $_COOKIE['UTM_MEDIUM'] ? urldecode($_COOKIE['UTM_MEDIUM']) : '',
                'UTM_TERM' => $_COOKIE['UTM_TERM'] ? urldecode($_COOKIE['UTM_TERM']) : '',
                'UTM_CONTENT' => $_COOKIE['UTM_CONTENT'] ? urldecode($_COOKIE['UTM_CONTENT']) : '',
                'UTM_CAMPAIGN' => $_COOKIE['UTM_CAMPAIGN'] ? urldecode($_COOKIE['UTM_CAMPAIGN']) : '',
            );

            $sa_stage->track_event($order_sdf['member_id'],'PayedOrderItem',$payed_order_item);
        }

        return true;
    }
}
