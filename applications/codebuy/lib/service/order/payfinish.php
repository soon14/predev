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


class codebuy_service_order_payfinish
{
    //结算后记录购买
    public function exec(&$bill, &$msg = '')
    {
        $order_id = $bill['order_id'];
        $member_id = $bill['member_id'];
        $mdl_activity = app::get('codebuy')->model('activity');
        $mdl_log = app::get('codebuy')->model('log');
        $mdl_order_items = app::get('b2c')->model('order_items');
        $order_item = $mdl_order_items->getRow('nums,goods_id',array('order_id'=>$order_id));
        $activity = $mdl_activity->getRow('id',array('goods_id'=>$order_item['goods_id'],'status'=>'0'));
        $log = $mdl_log->getRow('*',array(
                        'activity_id'=>$activity['id'],
                        'member_id'=>$member_id,
                        'order_id'=>0));
        if(!empty($log)){
            $flag = $mdl_log->update(array('order_id'=>$order_id,'number'=>$order_item['nums']),array('id'=>$log['id']));
        }
        return true;
    }
}
