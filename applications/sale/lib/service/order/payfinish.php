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


class sale_service_order_payfinish
{
    //结算后记录购买
    public function exec(&$bill, &$msg = '')
    {
        $order_id = $bill['order_id'];
        $member_id = $bill['member_id'];
        $mdl_reserve = app::get('sale')->model('reserve');
        $mdl_sale = app::get('sale')->model('sales');
        $mdl_order_items = app::get('b2c')->model('order_items');
        $order_item = $mdl_order_items->getRow('nums,goods_id',array('order_id'=>$order_id));
        $sale = $mdl_sale->getRow('id',array('goods_id'=>$order_item['goods_id'],'status'=>'0'));
        $reserve = $mdl_reserve->getRow('*',array(
                        'goods_id'=>$order_item['goods_id'],
                        'member_id'=>$member_id,
                        'sale_id'=>$sale['id'],
                        'status'=>'0'));
        if(!empty($reserve)){
            $flag = $mdl_reserve->update(array('status'=>'1','number'=>$order_item['nums']),array('goods_id'=>$order_item['goods_id'],'member_id'=>$member_id,'sale_id'=>$sale['id']));
        }
        return true;
    }
}
