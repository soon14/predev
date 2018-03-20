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


class preselling_tasks_activity_cancel extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        $mdl_activity = app::get('preselling')->model('activity');
        if($activity_list = $mdl_activity->getList('activity_id,status,is_refund',array('balance_endtime|lthan'=>time(),'status|noequal'=>'cancel'))) {
            $mdl_orders = app::get('preselling')->model('orders');
            $obj_stock = vmc::singleton('b2c_goods_stock');
            $mdl_bills = app::get('ectools')->model('bills');
            $this->user = vmc::singleton('desktop_user');
            foreach($activity_list as $activity) {
                $order_list = $mdl_orders->getList('presell_id,activity_id,nums,bn,order_total,member_id,pay_app,deposit_price,status',array('activity_id'=>$activity['activity_id'],'status|in'=>array('0','1')));
                foreach($order_list as $order) {
                    $order_update = array();
                    if($order['status'] == '1' && $activity['is_refund'] == 'true') {
                        $pay_bill = $mdl_bills->getRow('*',array('bill_id'=>$order['deposit_bill_id'],'bill_type'=>'payment','status|in'=>array('succ','progress')));
                        //已支付生成退款单
                        $bill_sdf = array(
                            'order_id' => $order['presell_id'],
                            'bill_type' => 'refund',
                            'pay_object' => 'porder',
                            'member_id' => $order['member_id'],
                            'op_id' => $this->user->user_id,
                            'status' => 'ready',
                            'pay_mode' => 'online',
                            'money' => $order['deposit_price'],
                            'pay_app_id' => $order['pay_app'],
                            'app_id' =>'preselling',
                            'transaction_id' => $pay_bill['out_trade_no'],
                            'memo' => '预售活动结束',
                        );
                        $bill_sdf['bill_id'] = $mdl_bills->apply_id($bill_sdf);
                        $order_update['deposit_pay_status'] = '2';
                        $order_update['deposit_refund_id'] = $bill_sdf['bill_id'];
                        if(!$mdl_bills->save($bill_sdf)) {
                            logger::error('生成退款单失败!预售订单号:'.$order['presell_id'].','.$msg);
                        };
                    }
                    //库存释放
                    $unfreeze_data[] = array(
                        'sku'=>$order['bn'],
                        'quantity'=>$order['nums']
                    );
                    if(!$obj_stock->unfreeze($unfreeze_data,$msg)){
                        logger::error('库存冻结释放异常!预售订单号:'.$order['presell_id'].','.$msg);
                    }
                    //修改订单状态
                    $order_update['status'] = '3';
                    if(!$mdl_orders->update($order_update,array('presell_id'=>$order['presell_id']))) {
                        logger::error('修改预售订单作废状态失败!预售订单号:'.$order['presell_id'].','.$msg);
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