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


class supplier_service_delivery_finish
{
    public function exec($delivery_sdf, &$msg)
    {

        $delivery_id = $delivery_sdf['delivery_id'];
        $mdl_supplier_reldelivery = app::get('supplier')->model('reldelivery');
        $reldelivery = $mdl_supplier_reldelivery->dump($delivery_id);
        if (!$reldelivery) {
            return true;
        }
        $mdl_supplier_relgoods = app::get('supplier')->model('relgoods');
        $mdl_voucher = app::get('supplier')->model('voucher');
        $mdl_supplier = app::get('supplier')->model('supplier');
        $mdl_delivery_items = app::get('b2c')->model('delivery_items');
        $delivery_items = $mdl_delivery_items->getList('*',array('delivery_id'=>$delivery_id));
        foreach ($delivery_items as $item) {
            $product_id_arr[] = $item['product_id'];
        }
        $relgoods = $mdl_supplier_relgoods->getList('*', array('supplier_id' => $reldelivery['supplier_id'], 'product_id' => $product_id_arr));
        $relgoods = utils::array_change_key($relgoods, 'product_id');
        $supplier =
        $voucher_data = array(
            'delivery_id' => $reldelivery['delivery_id'],
            'supplier_id' => $reldelivery['supplier_id'],
            'supplier_bn' => $reldelivery['supplier_bn'],
            'createtime' => time(),
        );
        $voucher_data['voucher_id'] = $mdl_voucher->apply_id();
        $omath = vmc::singleton('ectools_math');
        foreach ($delivery_items as $item) {
            $voucher_data['items'][] = array(
                'voucher_id' => $voucher_data['voucher_id'],
                'delivery_item_id' => $item['item_id'],
                'product_id' => $item['product_id'],
                'goods_id' => $item['goods_id'],
                'bn' => $item['bn'],
                'name' => $item['name'],
                'spec_info' => $item['spec_info'],
                'image_id' => $item['image_id'],
                's_num' => $item['sendnum'],
                's_price' => $relgoods[$item['product_id']]['purchase_price'],
                's_subprice' => $omath->number_multiple(array(
                     $item['sendnum'],
                     $relgoods[$item['product_id']]['purchase_price'],
                )),
            );
        }
        logger::alert($voucher_data);
        if ($mdl_voucher->save($voucher_data)) {
            foreach (vmc::servicelist('supplier.voucher.create') as $service) {
                if (!$service->exec($voucher_data, $msg)) {
                    logger::error($msg);
                    return false;
                }
            }
            return true;
        } else {
            $msg = '结算凭证生成失败.delivery_id:'.$delivery_id;
            return false;
        }
    }
}
