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
return array(
    // 默认
    '__' => array(
        'fields' => array(
            'member_id' => 'member_id',
            'memo' => 'memo',
            'pay_app' => 'pay_app',
            'dlytype_id' => 'dlytype_id',
            'createtime' => 'createtime',
            'need_shipping' => 'need_shipping',
            'need_invoice' => 'need_invoice',
            'platform' => 'platform',
            'is_cod' => 'is_cod',
            'consignee' => 'consignee',
            'order_id' => 'order_id',
            'weight' => 'weight',
            'quantity' => 'quantity',
            'ip' => 'ip',
            'memberlv_discount' => 'memberlv_discount',
            'pmt_goods' => 'pmt_goods',
            'pmt_order' => 'pmt_order',
            'finally_cart_amount' => 'finally_cart_amount',
            'score_g' => 'score_g',
            'order_total' => 'order_total',
            'cost_tax' => 'cost_tax',
            'cost_protect' => 'cost_protect',
            'cost_payment' => 'cost_payment',
            'cost_freight' => 'cost_freight',
            'items' => 'items',
        ),
        'output' => array(
        ),
    ),
    // order.create - 订单创建完成时触发 
    'create' => array(
        'fields' => array(
        ),
        'output' => array(
        ),
    ),
    // order.cancel - 订单作废完成时触发 
    'cancel' => array(
        'fields' => array(
        ),
        'output' => array(
        ),
    ),
    // order.end - 订单归档完成时触发 
    'end' => array(
        'fields' => array(
        ),
        'output' => array(
        ),
    ),
);
