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


class groupbooking_tasks_order_cancel extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        $mdl_orders = app::get('groupbooking')->model('orders');
        $autocancel_time = app::get('groupbooking')->getConf('groupbooking_order_autocancel_time');
        $filtertime = time()-$autocancel_time;
        if($order_list = $mdl_orders->getList('gb_id,nums,bn',array('createtime|lthan'=>$filtertime,'pay_status'=>'0','is_failure'=>'0','status'=>'0'))) {
            $obj_stock = vmc::singleton('b2c_goods_stock');
            $mdl_participate_member = app::get('groupbooking')->model('participate_member');
            foreach($order_list as $order) {
                //库存释放
                $unfreeze_data[] = array(
                    'sku'=>$order['bn'],
                    'quantity'=>$order['nums']
                );
                if(!$obj_stock->unfreeze($unfreeze_data,$msg)){
                    logger::error('库存冻结释放异常!团购订单号:'.$order['gb_id'].','.$msg);
                }
                if(!$mdl_orders->update(array('is_failure'=>'1'),array('gb_id'=>$order['gb_id']))) {
                    logger::error('修改团购订单作废状态失败!团购订单号:'.$order['gb_id'].','.$msg);
                };
                if(!$mdl_participate_member->update(array('status'=>'2'),array('gb_id'=>$order['gb_id']))) {
                    logger::error('修改参与用户状态修改， 拼团订单id'.$order['gb_id']);
                };
            }
        };
        return true;
    }

}