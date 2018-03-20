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


class vmcocean_order_delivery
{
    public function __construct($app)
    {
        $this->app = $app;
    }


    public function exec($delivery_sdf, &$msg = '')
    {
        if(!$delivery_sdf['delivery_items']){
            $delivery_sdf['delivery_items'] = app::get('b2c')->model('delivery_items')->getList('*',array('delivery_id'=>$delivery_sdf['delivery_id']));
        }
        $order_sdf = app::get('b2c')->model('orders')->dump($delivery_sdf['order_id']);
        $sa_stage = vmc::singleton('vmcocean_stage');
        $dlycorp = app::get('b2c')->model('dlycorp')->dump($delivery_sdf['dlycorp_id']);
        $delivery_data = array(
            '$ip'=>$order_sdf['ip'],
            'OrderId'=>$delivery_sdf['order_id'],
            'DeliveryId'=>$delivery_sdf['delivery_id'],
            'ConsigneeArea'=>explode(':',$delivery_sdf['consignee_area'])[1],
            'DlycorpName'=>$dlycorp['name'],
            'DlycorpCode'=>$dlycorp['corp_code'],
            'LogisticsNo'=>$delivery_sdf['logistics_no'],
            'CostFreight'=>(float)$delivery_sdf['cost_freight'],
            'UTM_SOURCE' => $_COOKIE['UTM_SOURCE'] ? urldecode($_COOKIE['UTM_SOURCE']) : '',
            'UTM_MEDIUM' => $_COOKIE['UTM_MEDIUM'] ? urldecode($_COOKIE['UTM_MEDIUM']) : '',
            'UTM_TERM' => $_COOKIE['UTM_TERM'] ? urldecode($_COOKIE['UTM_TERM']) : '',
            'UTM_CONTENT' => $_COOKIE['UTM_CONTENT'] ? urldecode($_COOKIE['UTM_CONTENT']) : '',
            'UTM_CAMPAIGN' => $_COOKIE['UTM_CAMPAIGN'] ? urldecode($_COOKIE['UTM_CAMPAIGN']) : '',
        );
        $sa_stage->track_event($delivery_sdf['member_id'],'DeliverySend',$delivery_data);
        foreach ($delivery_sdf['delivery_items'] as $key => $item) {
            $delivery_data_item = array(
                '$ip'=>$order_sdf['ip'],
                'DeliveryId'=>$delivery_sdf['order_id'],
                'ProductName'=>$item['name'],
                'ProductSKU'=>$item['bn'],
                'ProductSpec'=>$item['spec_info'],
                'ProductSendNum'=>(int)$item['sendnum'],
                'ProductWeightG'=>(float)$item['weight'],
                'ProductDBPId'=>$item['product_id'],
                'ProductDBGId'=>$item['goods_id'],
                'UTM_SOURCE' => $_COOKIE['UTM_SOURCE'] ? urldecode($_COOKIE['UTM_SOURCE']) : '',
                'UTM_MEDIUM' => $_COOKIE['UTM_MEDIUM'] ? urldecode($_COOKIE['UTM_MEDIUM']) : '',
                'UTM_TERM' => $_COOKIE['UTM_TERM'] ? urldecode($_COOKIE['UTM_TERM']) : '',
                'UTM_CONTENT' => $_COOKIE['UTM_CONTENT'] ? urldecode($_COOKIE['UTM_CONTENT']) : '',
                'UTM_CAMPAIGN' => $_COOKIE['UTM_CAMPAIGN'] ? urldecode($_COOKIE['UTM_CAMPAIGN']) : '',
            );

            $sa_stage->track_event($delivery_sdf['member_id'],'DeliverySendItem',$delivery_data_item);
        }

        return true;
    }
}
