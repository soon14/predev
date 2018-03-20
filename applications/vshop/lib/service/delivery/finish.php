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


class vshop_service_delivery_finish
{
    public function exec($delivery_sdf, &$msg)
    {
        $mdl_vshop_relorder = app::get('vshop')->model('relorder');
        $order_id = $delivery_sdf['order_id'];
        $delivery_id = $delivery_sdf['delivery_id'];
        $relorder = $mdl_vshop_relorder->getRow('*',array('order_id'=>$order_id));
        if(!$relorder){
            return true;
        }
        $mdl_vshop = app::get('vshop')->model('shop');
        $mdl_vshop_lv = app::get('vshop')->model('lv');
        $vshop = $mdl_vshop->dump($relorder['shop_id']);
        if(!$vshop){
            return true;
        }
        $vshop_lv = $mdl_vshop_lv->dump($vshop['shop_lv_id']);
        $default_profit = app::get('vshop')->getConf('default_profit');//默认分润比例
        $coefficient = $vshop_lv['coefficient'];//分润系数
        $mdl_vshop_reldelivery = app::get('vshop')->model('reldelivery');
        $mdl_vshop_relprofit = app::get('vshop')->model('relprofit');
        $mdl_voucher = app::get('vshop')->model('voucher');
        $mdl_delivery_items = app::get('b2c')->model('delivery_items');
        $mdl_order_items = app::get('b2c')->model('order_items');
        $order_items = $mdl_order_items->getList('*',array('order_id'=>$order_id));
        $order_items = utils::array_change_key($order_items, 'item_id');
        $delivery_items = $mdl_delivery_items->getList('*',array('delivery_id'=>$delivery_id));
        foreach ($delivery_items as $item) {
            $product_id_arr[] = $item['product_id'];
        }
        $relprofit = $mdl_vshop_relprofit->getList('*', array('product_id' => $product_id_arr));
        $relprofit = utils::array_change_key($relprofit, 'product_id');
        //结算凭证
        $voucher_data = array(
            'order_id' => $order_id,
            'delivery_id' => $delivery_id,
            'shop_id' => $vshop['shop_id'],
            'shop_name' => $vshop['name'],
            'createtime' => time(),
        );
        $voucher_data['voucher_id'] = $mdl_voucher->apply_id();
        $omath = vmc::singleton('ectools_math');
        $total_subprice = 0;
        foreach ($delivery_items as $item) {
            if($item['item_type']!='product'){
                continue;
            }
            $profit = ($relprofit[$item['product_id']]?$relprofit[$item['product_id']]['share']:$default_profit);
            $order_item = $order_items[$item['order_item_id']];
            $price = (app::get('profit_by_buyprice')=='true'?$order_item['buy_price']:$order_item['price']);
            if($profit>=1){
                $_s_price = $profit;//单件商品分润
            }else{
                $_s_price = $omath->number_multiple(array(
                     $price,
                     $profit,
                ));
            }
            $_s_price = $omath->number_multiple(array(
                 $_s_price,
                 $coefficient,
            ));
            $s_subprice = $omath->number_multiple(array(
                 $item['sendnum'],
                 $_s_price,
            ));
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
                'price'=>$order_item['price'],
                'buy_price'=>$order_item['buy_price'],
                'profit'=>$profit,
                's_price' => $_s_price,
                's_subprice' =>$s_subprice,
            );
            $total_subprice+= $s_subprice;
        }
        $voucher_data['total_subprice'] = $total_subprice;
        if ($mdl_voucher->save($voucher_data)) {
            foreach (vmc::servicelist('vshop.voucher.create') as $service) {
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
