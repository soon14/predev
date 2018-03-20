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
            'createtime' => 'createtime',
            'last_modified' => 'last_modified',
            'status' => 'status',
            'confirm' => 'confirm',
            'pay_status' => 'pay_status',
            'payed' => 'payed',
            'is_cod' => 'is_cod',
            'need_shipping' => 'need_shipping',
            'ship_status' => 'ship_status',
            'pay_app' => 'pay_app',
            'dlytype_id' => 'dlytype_id',
            'member_id' => 'member_id',
            'weight' => 'weight',
            'quantity' => 'quantity',
            'need_invoice' => 'need_invoice',
            'invoice_title' => 'invoice_title',
            'finally_cart_amount' => 'finally_cart_amount',
            'cost_freight' => 'cost_freight',
            'cost_protect' => 'cost_protect',
            'cost_payment' => 'cost_payment',
            'cost_tax' => 'cost_tax',
            'currency' => 'currency',
            'cur_rate' => 'cur_rate',
            'memberlv_discount' => 'memberlv_discount',
            'pmt_goods' => 'pmt_goods',
            'pmt_order' => 'pmt_order',
            'memo' => 'memo',
            'remarks' => 'remarks',
            'addon' => 'addon',
            'items' => 'items',
            'consignee' => 'consignee',
        ),
        'input' => array(
        ),
        'output' => array(
        ),
    ),
    // order.read.getbyId - 获取单个订单 
    'read_getbyId' => array(
        'fields' => array(
        ),
        'input' => array(
            'order_id' => 'order_id',
            'fields' => 'fields',
        ),
        'output' => array(
        ),
    ),
    // order.read.search - 订单检索 
    'read_search' => array(
        'fields' => array(
            'order_id' => 'order_id',
            'createtime' => 'createtime',
            'last_modified' => 'last_modified',
            'status' => 'status',
            'confirm' => 'confirm',
            'pay_status' => 'pay_status',
            'payed' => 'payed',
            'is_cod' => 'is_cod',
            'need_shipping' => 'need_shipping',
            'ship_status' => 'ship_status',
            'pay_app' => 'pay_app',
            'dlytype_id' => 'dlytype_id',
            'member_id' => 'member_id',
            'weight' => 'weight',
            'quantity' => 'quantity',
            'need_invoice' => 'need_invoice',
            'invoice_title' => 'invoice_title',
            'finally_cart_amount' => 'finally_cart_amount',
            'cost_freight' => 'cost_freight',
            'cost_protect' => 'cost_protect',
            'cost_payment' => 'cost_payment',
            'cost_tax' => 'cost_tax',
            'currency' => 'currency',
            'cur_rate' => 'cur_rate',
            'memberlv_discount' => 'memberlv_discount',
            'pmt_goods' => 'pmt_goods',
            'pmt_order' => 'pmt_order',
            'memo' => 'memo',
            'remarks' => 'remarks',
            'addon' => 'addon',
        ),
        'input' => array(
            'start_date' => 'startDate',
            'end_date' => 'endDate',
            'page' => 'page',
            'page_size' => 'pageSize',
            'order_state' => 'order_state',
            'sort_type' => 'sort_type',
            'date_type' => 'date_type',
            'fields' => 'fields',
        ),
        'output' => array(
        ),
    ),
    // order.read.notPayOrderInfo - 批量查询未付款订单 
    'read_notPayOrderInfo' => array(
        'fields' => array(
        ),
        'input' => array(
            'start_date' => 'startDate',
            'end_date' => 'endDate',
            'page' => 'page',
            'page_size' => 'pageSize',
            'fields' => 'fields',
        ),
        'output' => array(
        ),
    ),
    // order.read.notPayOrderById - 未付款订单单条记录查询 
    'read_notPayOrderById' => array(
        'fields' => array(
        ),
        'input' => array(
            'order_id' => 'order_id',
            'fields' => 'fields',
        ),
        'output' => array(
        ),
    ),
    // order.read.remarkByOrderId - 查询商家备注 
    'read_remarkByOrderId' => array(
        'fields' => array(
            'remarks' => 'remarks',
        ),
        'input' => array(
            'order_id' => 'order_id',
        ),
        'output' => array(
        ),
    ),
    // order.write.remarkUpdate - 商家订单备注修改 
    'write_remarkUpdate' => array(
        'fields' => array(
            'order_id' => 'order_id',
            'modified' => 'modified',
        ),
        'input' => array(
            'order_id' => 'order_id',
            'remarks' => 'remark',
        ),
        'output' => array(
        ),
    ),
    // order.bill.write.pay - 订单付款 
    'bill_write_pay' => array(
        'fields' => array(
            'order_id' => 'order_id',
            'modified' => 'modified',
        ),
        'input' => array(
            'order_id' => 'order_id',
            'pay_money' => 'money',
            'pay_mode' => 'pay_mode',
            'pay_app_id' => 'pay_app_id',
            'payee_account' => 'payee_account',
        ),
        'output' => array(
        ),
    ),
    // order.bill.write.refund - 订单退款 
    'bill_write_refund' => array(
        'fields' => array(
            'order_id' => 'order_id',
            'modified' => 'modified',
        ),
        'input' => array(
            'order_id' => 'order_id',
            'pay_mode' => 'pay_mode',
            'pay_app_id' => 'pay_app_id',
            'payee_account' => 'payee_account',
            'payee_bank' => 'payee_bank',
            'out_trade_no' => 'out_trade_no',
            'pay_fee' => 'pay_fee',
            'payer_bank' => 'payer_bank',
            'payer_account' => 'payer_account',
            'memo' => 'memo',
        ),
        'output' => array(
        ),
    ),
    // order.delivery.write.send - 订单发货 
    'delivery_write_send' => array(
        'fields' => array(
            'order_id' => 'order_id',
            'modified' => 'modified',
            'logistics_no' => 'logistics_no',
            'delivery_id' => 'delivery_id',
            'delivery_type' => 'delivery_type',
            'send_router' => 'send_router',
            'consignor' => 'consignor',
            'consignee' => 'consignee',
            'status' => 'status',
        ),
        'input' => array(
            'order_id' => 'order_id',
            'dlycorp_id' => 'dlycorp_id',
            'dlyplace_id' => 'dlyplace_id',
            'send_router' => 'send_router',
            'logistics_no' => 'logistics_no',
            'cost_freight' => 'cost_freight',
            'memo' => 'memo',
            'send' => 'send',
        ),
        'output' => array(
        ),
    ),
    // order.delivery.write.reship - 订单退货 
    'delivery_write_reship' => array(
        'fields' => array(
            'order_id' => 'order_id',
            'modified' => 'modified',
            'logistics_no' => 'logistics_no',
            'delivery_id' => 'delivery_id',
            'delivery_type' => 'delivery_type',
            'send_router' => 'send_router',
            'consignor' => 'consignor',
            'consignee' => 'consignee',
            'status' => 'status',
        ),
        'input' => array(
            'order_id' => 'order_id',
            'dlycorp_id' => 'dlycorp_id',
            'dlyplace_id' => 'dlyplace_id',
            'send_router' => 'send_router',
            'logistics_no' => 'logistics_no',
            'cost_freight' => 'cost_freight',
            'memo' => 'memo',
            'send' => 'send',
        ),
        'output' => array(
        ),
    ),
    // order.write.cancel - 订单作废 
    'write_cancel' => array(
        'fields' => array(
            'order_id' => 'order_id',
            'modified' => 'modified',
        ),
        'input' => array(
            'order_id' => 'order_id',
        ),
        'output' => array(
        ),
    ),
    // order.write.end - 订单归档完成 
    'write_end' => array(
        'fields' => array(
            'order_id' => 'order_id',
            'modified' => 'modified',
        ),
        'input' => array(
            'order_id' => 'order_id',
        ),
        'output' => array(
        ),
    ),
);
