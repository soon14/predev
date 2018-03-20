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


class supplier_service_order_payfinish
{
    public function exec(&$bill, &$msg)
    {
        $order_id = $bill['order_id'];
        $mdl_order = app::get('b2c')->model('orders');
        $order = $mdl_order->dump($order_id, '*', array('items' => array('*')));
        $order_items = $order['items'];
        foreach ($order_items as $item) {
            $product_id_arr[] = $item['product_id'];
        }
        $mdl_supplier_relgoods = app::get('supplier')->model('relgoods');
        $relgoods = $mdl_supplier_relgoods->getList('*', array(
            'product_id' => $product_id_arr,
        ));
        if (!$relgoods) {
            return true;
        }
        $relgoods = utils::array_change_key($relgoods, 'product_id');

        foreach ($order_items as &$item) {
            if ($relgoods[$item['product_id']]) {
                $item['supplier_id'] = $relgoods[$item['product_id']]['supplier_id'];
            } else {
                $item['supplier_id'] = 0;
            }
        }
        $order_items_group = utils::array_change_key($order_items, 'supplier_id', true);
        $obj_delivery = vmc::singleton('b2c_order_delivery');
        $mdl_supplier_reldelivery = app::get('supplier')->model('reldelivery');
        $db = vmc::database();
        $db->beginTransaction();
        foreach ($order_items_group as $supplier_id => $order_items) {
            if ($supplier_id == 0) {
                continue;
            }
            $supplier = app::get('supplier')->model('supplier')->dump($supplier_id);
            if (empty($supplier['dlyplace_send'])) {
                continue;//供应商未绑定发货地,不拆发货单
            }
            $dlyplace = app::get('b2c')->model('dlyplace')->dump($supplier['dlyplace_send']);
            $delivery_sdf = array(
                'order_id' => $order_id,
                'delivery_type' => 'send', //发货
                'member_id' => $order['member_id'],
                // 'send_router' => 'tpwarehouse',//第三方仓发货（供应商代发）
                'dlyplace_id' => $supplier['dlyplace_send'],
                'cost_freight' => 0,
                'consignor' => $dlyplace['consignor'],
                'consignee' => $order['consignee'], //sdf array
                'status' => 'ready',
                'memo' => '供应商代发.'.$supplier['supplier_name'].'('.$supplier['supplier_bn'].')',
            );
            if ($dlyplace['dp_type'] == 'tpwarehouse') {
                $delivery_sdf['send_router'] = 'tpwarehouse';
            } else {
                $delivery_sdf['send_router'] = 'selfwarehouse';
            }
            $send_arr = array(); // array('$order_item_id'=>$sendnum);
            foreach ($order_items as $item_v) {
                $send_arr[$item_v['item_id']] = $item_v['nums'];
            }

            if (!$obj_delivery->generate($delivery_sdf, $send_arr, $msg)) {
                $has_error = true;
                $error_msg = $msg;
                break;
            }
            $reldelivery = array(
                'delivery_id' => $delivery_sdf['delivery_id'],
                'supplier_id' => $supplier_id,
                'supplier_bn' => $supplier['supplier_bn'],
            );
            if (!$obj_delivery->save($delivery_sdf, $msg) || !$mdl_supplier_reldelivery->save($reldelivery)) {
                $has_error = true;
                $error_msg = $msg;
                break;
            }
            $new_reldelivery_arr[] = $reldelivery;
        }
        if ($has_error) {
            logger::error('供应商代发货类型发货单生成失败:'.$error_msg);
            $db->rollback();//数据库事务操作回滚
            return false;
        } else {
            foreach (vmc::servicelist('supplier.reldelivery.create') as $service) {
                if (!$service->exec($new_reldelivery_arr, $msg)) {
                    logger::error($msg);
                    $db->rollback();//数据库事务操作回滚
                    return false;
                }
            }
            $db->commit();//数据库事务操作提交
            return true;
        }
    }
}
