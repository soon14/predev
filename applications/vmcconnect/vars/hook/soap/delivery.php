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
            'order_id' => 'order_id',
            'delivery_type' => 'delivery_type',
            'member_id' => 'member_id',
            'dlycorp_id' => 'dlycorp_id',
            'dlyplace_id' => 'dlyplace_id',
            'send_router' => 'send_router',
            'logistics_no' => 'logistics_no',
            'cost_freight' => 'cost_freight',
            'consignor' => 'consignor',
            'consignee' => 'consignee',
            'status' => 'status',
            'memo' => 'memo',
            'delivery_id' => 'delivery_id',
            'createtime' => 'createtime',
            'delivery_items' => 'delivery_items',
            'finally_cart_amount' => 'finally_cart_amount',
            'total_discount' => 'total_discount',
            'need_invoice' => 'need_invoice',
            'invoice_title' => 'invoice_title',
            'invoice_addon' => 'invoice_addon',
        ),
        'output' => array(
        ),
    ),
    // delivery.send.create - 发货单据创建完成 
    'send_create' => array(
        'fields' => array(
        ),
        'output' => array(
        ),
    ),
    // delivery.send.update - 后台发货单据更新 
    'send_update' => array(
        'fields' => array(
        ),
        'output' => array(
        ),
    ),
    // delivery.send.finish - 发货操作完成 
    'send_finish' => array(
        'fields' => array(
        ),
        'output' => array(
        ),
    ),
    // delivery.reship.create - 退货单据创建完成 
    'reship_create' => array(
        'fields' => array(
        ),
        'output' => array(
        ),
    ),
    // delivery.reship.update - 退货单据更新 
    'reship_update' => array(
        'fields' => array(
        ),
        'output' => array(
        ),
    ),
    // delivery.reship.finish - 退货单据操作完成 
    'reship_finish' => array(
        'fields' => array(
        ),
        'output' => array(
        ),
    ),
);
