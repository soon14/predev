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
        'name' => '类目',
        'items' =>
        array(
            'category.write.add' => '添加分类',
            'category.write.delete' => '删除分类',
            'category.write.update' => '更新分类',
            'category.read.getAll' => '获取所有类目信息',
            'category.read.getFront' => '获取前台展示的分类',
            'category.read.findById' => '获取单个类目信息',
            'category.read.findByPId' => '查找子类目列表',
        ),
    ),
    'goodattrs' =>
    array(
        'name' => '商品类型',
        'items' =>
        array(
            'goodattrs.read.get' => '获取商品类型列表',
            'goodattrs.read.valuesByAttrId' => '获取商品类型属性',
        ),
    ),
    'goods' =>
    array(
        'name' => '商品',
        'items' =>
        array(
            'goods.write.add' => '新增商品',
            'goods.write.update' => '修改商品',
            'goods.write.upOrDown' => '商品上下架',
            'goods.read.byId' => '获取单个商品',
            'goods.sku.read.findSkuById' => '获取单个SKU',
            'goods.sku.stock.read.find' => '获取sku库存信息',
            'goods.sku.stock.write.update' => '设置sku库存',
        ),
    ),
    'order' =>
    array(
        'name' => '订单',
        'items' =>
        array(
            'order.read.getbyId' => '获取单个订单',
            'order.read.search' => '订单检索',
            'order.read.notPayOrderInfo' => '批量查询未付款订单',
            'order.read.notPayOrderById' => '未付款订单单条记录查询',
            'order.read.remarkByOrderId' => '查询商家备注',
            'order.write.remarkUpdate' => '商家订单备注修改',
            'order.bill.write.pay' => '订单付款',
            'order.bill.write.refund' => '订单退款',
            'order.delivery.write.send' => '订单发货',
            'order.delivery.write.reship' => '订单退货',
            'order.write.cancel' => '订单作废',
            'order.write.end' => '订单归档完成',
        ),
    ),
    'pay' =>
        array(
            'name' => '支付',
            'items' =>
                array(
                    'pay.read.get' => '查询支付方式',
                ),
        ),
    'distribution' =>
        array(
            'name' => '配送',
            'items' =>
                array(
                    'distribution.read.get' => '查询配送方式',
                ),
        ),
    'delivery' =>
    array(
        'name' => '物流',
        'items' =>
        array(
            'delivery.write.add' => '添加物流公司',
            'delivery.write.edit' => '添加物流公司',
            'delivery.write.delete' => '删除物流公司',
            'delivery.read.get' => '获取物流公司',
        ),
    ),
    'areas' =>
    array(
        'name' => '地址库',
        'items' =>
        array(
            'areas.read.province.get' => '获取省级地址列表',
            'areas.read.city.get' => '获取市级信息列表',
            'areas.read.county.get' => '获取区县级信息列表',
            'areas.read.town.get' => '获取乡镇级信息列表',
        ),
    ),
    'refundapply' =>
    array(
        'name' => '退款',
        'items' =>
        array(
            'refundapply.read.queryPageList' => '退款审核单列表查询',
            'refundapply.read.queryById' => '根据Id查询退款审核单',
            'refundapply.read.getWaitRefundNum' => '待处理退款单数查询',
            'refundapply.write.replyRefund' => '审核退款单',
        ),
    ),
    'system' =>
    array(
        'name' => '系统',
        'items' =>
        array(
            'system.ping' => 'PING',
            'system.read.setting.info' => '查询基本信息',
            'system.read.setting.pc' => '查询PC版基本信息',
            'system.read.setting.mobile' => '查询手机版基本信息',
            'system.returnaddress.read.get' => '查询退货地址列表',
            'system.returnaddress.read.getdef' => '查询默认退货地址',
            'system.shipaddress.read.get' => '查询发货地址列',
            'system.shipaddress.read.getdef' => '查询默认发货地址',
        ),
    ),
);
