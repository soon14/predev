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


class groupbooking_tasks_activity_cancel extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        $mdl_activity = app::get('groupbooking')->model('activity');
        if($activity_list = $mdl_activity->getList('activity_id,status',array('end_time|lthan'=>time(),'status|noequal'=>'cancel'))) {
            $mdl_orders = app::get('groupbooking')->model('orders');
            $obj_stock = vmc::singleton('b2c_goods_stock');
            $mdl_bills = app::get('ectools')->model('bills');
            $mdl_participate_member = app::get('groupbooking')->model('participate_member');
            $this->user = vmc::singleton('desktop_user');
            foreach($activity_list as $activity) {
                $order_list = $mdl_orders->getList('gb_id,activity_id,nums,bn,order_total,member_id,pay_status,pay_app,payed',array('activity_id'=>$activity['activity_id'],'status'=>'0','is_failure'=>'0'));
                foreach($order_list as $order) {
                    if($order['pay_status'] == '1' || $order['pay_status'] == '2') {
                        $pay_bill = $mdl_bills->getRow('*',array('order_id'=>$order['gb_id'],'bill_type'=>'payment','status|in'=>array('succ','progress'),'out_trade_no|noequal'=>''));
                        //已支付生成退款单
                        $bill_sdf = array(
                            'order_id' => $order['gb_id'],
                            'bill_type' => 'refund',
                            'pay_object' => 'gborder',
                            'member_id' => $order['member_id'],
                            'op_id' => $this->user->user_id,
                            'status' => 'ready',
                            'pay_mode' => 'online',
                            'money' => $order['payed'],
                            'pay_app_id' => $order['pay_app'],
                            'app_id' =>'groupbooking',
                            'transaction_id' => $pay_bill['out_trade_no'],
                            'memo' => '拼团活动结束',
                        );
                        $bill_sdf['bill_id'] = $mdl_bills->apply_id($bill_sdf);
                        if(!$mdl_bills->save($bill_sdf)) {
                            logger::error('生成退款单失败!团购订单号:'.$order['gb_id'].','.$msg);
                        };
                    }
                    //库存释放
                    $unfreeze_data[] = array(
                        'sku'=>$order['bn'],
                        'quantity'=>$order['nums']
                    );
                    if(!$obj_stock->unfreeze($unfreeze_data,$msg)){
                        logger::error('库存冻结释放异常!团购订单号:'.$order['gb_id'].','.$msg);
                    }
                    //修改订单状态
                    if(!$mdl_orders->update(array('is_failure'=>'1'),array('gb_id'=>$order['gb_id']))) {
                        logger::error('修改团购订单作废状态失败!团购订单号:'.$order['gb_id'].','.$msg);
                    };
                    if(!$mdl_participate_member->update(array('status'=>'2'),array('gb_id'=>$order['gb_id']))) {
                        logger::error('修改参与用户状态修改， 拼团订单id'.$order['gb_id']);
                    };
                }
                //修改活动状态
                if(!$mdl_activity->update(array('status'=>'cancel'),array('activity_id'=>$activity['activity_id']))) {
                    logger::error('修改活动状态失败!活动ID:'.$activity['activity_id'].','.$msg);
                };
            }
        };
        return true;
    }


}