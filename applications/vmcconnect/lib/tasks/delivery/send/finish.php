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
class vmcconnect_tasks_delivery_send_finish extends vmcconnect_tasks_base implements base_interface_task{

    public function exec($params = null) {
        $task && $task_id = $task['task_id'];
        !$task_id && $task_id = isset($params['task_id']) && is_numeric($params['task_id']) ? $params['task_id'] : 0;
        if (!$task_id) return true;
        !$task && $task = $this->get_task($task_id);
        if (!$task) return true;
        //开始组装数组
        $total_statement = app::get('b2c')->model('orders');

        $total_res = $total_statement->getlist(
            'memberlv_discount,
                           pmt_goods,
                           pmt_order,
                           order_total,
                           finally_cart_amount,
                           need_invoice,
                           invoice_title,
                           invoice_addon',
            array('order_id'=>$task['task_data']['data']['order_id']));
        //组装发票部分
        $task['task_data']['data']['need_invoice'] = $total_res[0]['need_invoice'];
        $task['task_data']['data']['invoice_title'] = $total_res[0]['invoice_title'];
        $task['task_data']['data']['invoice_addon'] = $total_res[0]['invoice_addon'];
        //组装明细金额
        $detail_statment = app::get('b2c')->model('order_items');
        $detail_res = $detail_statment->getlist(
            'bn,
                             price,
                             member_lv_price,
                             buy_price,
                             amount',
            array('order_id'=>$task['task_data']['data']['order_id']));

        //总优惠的金额
        $total_discount = null;   //优惠总金额
        $total_pay = null;   //优惠后实付金额

        //组装delivery_items
        $delivery_items = app::get('b2c')->model('delivery_items')->getlist('*',array('delivery_id'=>$task['task_data']['data']['delivery_id']));
        $task['task_data']['data']['delivery_items'] = $delivery_items;

        foreach ($task['task_data']['data']['delivery_items'] as $key=>$val){
            foreach ($detail_res as $k=>$v){

                if ($val['bn'] == $v['bn']){
                    $task['task_data']['data']['delivery_items'][$key]['price'] = $v['price'];
                    $task['task_data']['data']['delivery_items'][$key]['member_lv_price'] = $v['member_lv_price'];
                    $task['task_data']['data']['delivery_items'][$key]['buy_price'] = $v['buy_price'];
                    $task['task_data']['data']['delivery_items'][$key]['amount'] =
                        substr(sprintf("%.4f", $val['sendnum']*$v['buy_price']),0,-1);

                    //优惠总金额
                    $total_discount .= ($v['price']-$v['buy_price'])*$val['sendnum'];
                    //优惠后实付金额
                    $total_pay += $v['buy_price']*$val['sendnum'];

                }
            }
        }

        $total_discount = substr(sprintf("%.4f", $total_discount),0,-1);
        $total_pay = substr(sprintf("%.4f", $total_pay),0,-1);
        //组装总金额部分
        $task['task_data']['data']['total_discount'] = $total_discount;
        $task['task_data']['data']['finally_cart_amount'] = $total_pay;

        return $this->_exec_task($params, $task);
    }

}
