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
    'category' =>
    array(
        'name' => '分类',
        'items' =>
        array(
            'category.save' => '添加分类',
            'category.remove' => '移除分类',
        ),
    ),
    'goods' =>
    array(
        'name' => '商品',
        'items' =>
        array(
            'goods.create' => '创建商品',
            'goods.update' => '更新商品',
            'goods.delete' => '删除商品',
        ),
    ),
    'products' =>
    array(
        'name' => '产品',
        'items' =>
        array(
            'products.create' => '创建产品',
            'products.update' => '更新产品',
            'products.delete' => '删除产品',
        ),
    ),
    'order' =>
    array(
        'name' => '订单',
        'items' =>
        array(
            'order.create' => '订单创建完成时触发',
            'order.cancel' => '订单作废完成时触发',
            'order.end' => '订单归档完成时触发',
        ),
    ),
    'biils' =>
    array(
        'name' => '支付',
        'items' =>
        array(
            'biils.payment.succ' => '订单支付完成',
            'biils.payment.progress' => '订单支付到担保方完成',
            'biils.refund.succ' => '订单退款完成',
            'biils.refund.progress' => '订单退款到担保方完成',
        ),
    ),
    'delivery' =>
    array(
        'name' => '退送货',
        'items' =>
        array(
            'delivery.send.create' => '发货单据创建完成',
            'delivery.send.update' => '后台发货单据更新',
            'delivery.send.finish' => '发货操作完成',
            'delivery.reship.create' => '退货单据创建完成',
            'delivery.reship.update' => '退货单据更新',
            'delivery.reship.finish' => '退货单据操作完成',
        ),
    ),
    'stock' =>
    array(
        'name' => '库存',
        'items' =>
        array(
            'stock.update' => '更新库存',
        ),
    ),
);
