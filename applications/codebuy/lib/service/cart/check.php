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


class codebuy_service_cart_check
{
    //加入\更新购物车时验证
    public function check($object, &$msg)
    {
        $mdl_products = app::get('b2c')->model('products');
        $mdl_activity = app::get('codebuy')->model('activity');
        $mdl_log = app::get('codebuy')->model('log');
        $member_id = $object['member_id'];
        $goods_id = $mdl_products->getRow('goods_id',array('product_id'=>$object['params']['item']['product_id']));
        $activity = $mdl_activity->getRow('id,number',array('goods_id'=>$goods_id['goods_id'],'status'=>'0','start|lthan'=>$now,'end|than'=>$now));
        if(!empty($activity)){
            if($object['quantity'] > $activity['number']){
                $msg = '此商品每人限购'.$activity['number'].'件，您超出了购买数量';
                return false;
            }
            $log = $mdl_log->getRow('id',array('member_id'=>$member_id,'activity_id'=>$activity['id'],'order_id'=>0));
            if(!empty($log)){
                return true;
            }else{
                $msg = '您没有优购码';
                return false;
            }
        }
        return true;
    }
}
