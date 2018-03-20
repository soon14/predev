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


class sale_service_cart_check
{
    //加入\更新购物车时验证
    public function check($object, &$msg)
    {
        $mdl_products = app::get('b2c')->model('products');
        $mdl_reserve = app::get('sale')->model('reserve');
        $mdl_sales = app::get('sale')->model('sales');
        $member_id = $object['member_id'];
        $goods_id = $mdl_products->getRow('goods_id',array('product_id'=>$object['params']['item']['product_id']));
        $sale = $mdl_sales->getRow('id,number,buy_mode,start',array('goods_id'=>$goods_id['goods_id'],'status'=>'0'));
        if(!empty($sale)){
            if($sale['start'] > time()){
                $msg = '购买暂未开始';
                return false;
            }
            if($object['quantity'] > $sale['number']){
                $msg = '此商品每人限购'.$sale['number'].'件，您超出了购买数量';
                return false;
            }
            $reserve = $mdl_reserve->getRow('*',array('goods_id'=>$goods_id['goods_id'],'member_id'=>$member_id,'sale_id'=>$sale['id']));
            if(!empty($reserve)){
                if($reserve['status'] == '1'){
                    $msg = '您已经购买了此商品';
                    return false;
                }
                return true;
            }else{
                if($sale['buy_mode'] =='0'){
                    $msg = '您未预约';
                    return false;
                }
            }
        }
        return true;
    }
}
