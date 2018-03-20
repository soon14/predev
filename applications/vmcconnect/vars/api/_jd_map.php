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
    // category
    'sellercat.add' => 'category.write.add',
    'sellercat.delete' => 'category.write.delete',
    'sellercat.update' => 'category.write.update',
    'warecats.get' => 'category.read.getAll',
    'sellercats.get' => 'category.read.getFront',
    'category.read.findById' => 'category.read.findById',
    'category.read.findByPId' => 'category.read.findByPId',
    // goodattrs
    'goodattrs.read.get' => 'goodattrs.read.get',
    'goodattrs.read.valuesByAttrId' => 'goodattrs.read.valuesByAttrId',
    // goods
    'ware.add' => 'goods.write.add',
    'ware.update' => 'goods.write.update',
    'goods.write.upOrDown' => 'goods.write.upOrDown',
    'goods.read.byId' => 'goods.read.byId',
    'goods.sku.read.findSkuById' => 'goods.sku.read.findSkuById',
    'goods.sku.stock.read.find' => 'goods.sku.stock.read.find',
    'goods.sku.stock.write.update' => 'goods.sku.stock.write.update',
    // order
    'order.read.getbyId' => 'order.read.getbyId',
    'order.read.search' => 'order.read.search',
    'order.read.notPayOrderInfo' => 'order.read.notPayOrderInfo',
    'order.read.notPayOrderById' => 'order.read.notPayOrderById',
    'order.read.remarkByOrderId' => 'order.read.remarkByOrderId',
    'order.write.remarkUpdate' => 'order.write.remarkUpdate',
    'order.bill.write.pay' => 'order.bill.write.pay',
    'order.bill.write.refund' => 'order.bill.write.refund',
    'order.delivery.write.send' => 'order.delivery.write.send',
    'order.delivery.write.reship' => 'order.delivery.write.reship',
    'order.write.cancel' => 'order.write.cancel',
    'order.write.end' => 'order.write.end',
    // delivery
    'delivery.write.add' => 'delivery.write.add',
    'delivery.write.edit' => 'delivery.write.edit',
    'delivery.write.delete' => 'delivery.write.delete',
    'delivery.read.get' => 'delivery.read.get',
    // areas
    'areas.read.province.get' => 'areas.read.province.get',
    'areas.read.city.get' => 'areas.read.city.get',
    'areas.read.county.get' => 'areas.read.county.get',
    'areas.read.town.get' => 'areas.read.town.get',
    // refundapply
    'refundapply.read.queryPageList' => 'refundapply.read.queryPageList',
    'refundapply.read.queryById' => 'refundapply.read.queryById',
    'refundapply.read.getWaitRefundNum' => 'refundapply.read.getWaitRefundNum',
    'refundapply.write.replyRefund' => 'refundapply.write.replyRefund',
    // system
    'system.ping' => 'system.ping',
    'system.read.setting.info' => 'system.read.setting.info',
    'system.read.setting.pc' => 'system.read.setting.pc',
    'system.read.setting.mobile' => 'system.read.setting.mobile',
    'system.returnaddress.read.get' => 'system.returnaddress.read.get',
    'system.returnaddress.read.getdef' => 'system.returnaddress.read.getdef',
    'system.shipaddress.read.get' => 'system.shipaddress.read.get',
    'system.shipaddress.read.getdef' => 'system.shipaddress.read.getdef',
);
