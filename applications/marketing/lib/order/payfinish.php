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
class marketing_order_payfinish{

    /**
     * 公开构造方法.
     *
     * @params app object
     */
    public function __construct($app)
    {
        $this->app = $app;
    }
    /**
     * 订单支付后的处理.
     *
     * @params array 支付完的信息
     * @params 支付时候成功的信息
     */
    public function exec(&$bill, &$msg = '')
    {
        $task_id = $_COOKIE['taskno'];

        if(!$task_id){
            return true;
        }
        //保留原来的交易单信息，从组数据可能修改过原有的信息
        $prototype_bill = $bill;
        $orders_sdf_arr = $this->get_orders($bill);
        if (empty($orders_sdf_arr)) {
            $msg = '未知订单ID';

            return false;
        }
        $report_mdl = $this ->app ->model('report');
        $order_mdl = app::get('b2c') ->model('orders');
        $task_order_mdl = $this ->app ->model('task_orders');
        foreach($orders_sdf_arr as $bill) {
            $order_id = $bill['order_id'];
            if (!$order_id) {
                $msg = '未知订单ID';
                return false;
            }
            $order_sdf =$order_mdl ->dump($order_id);
            if($order_sdf['pay_status'] !='1'){
                continue;
            }
            if($task_order_mdl ->count(array('order_id'=>$order_id))){
                continue;
            }
            $task = $this->app ->model('message_tasks') ->getRow('*' ,array('task_id' =>$task_id));
            if(!$task){
                setcookie('taskno',null,time()-3600,'/');
                return true;
            }
            $data =array(
                'order_id'=> $bill['order_id'],
                'task_id'=>$task_id,
                'member_id'=>$bill['member_id'],
                'createtime'=>time()
            );
            if(!$task_order_mdl->save($data)){
                return false;
            }
            $report = $report_mdl ->getRow('*', array('task_id' =>$task_id));
            $report['order_count'] +=1;
            $report['order_total']+=$order_sdf['order_total'];
            if(!$report_mdl ->save($report)){
                return false;
            }
        }
        $bill = $prototype_bill;
        return true;
    }

    private function get_orders($bill)
    {
        $bills = array();
        if ($bill['order_id']) {
            $bills[] =$bill;
        }
        //返回的bills值，是可能是一种情况。
        foreach (vmc::servicelist('merchant.get.order') as $service) {
            if ($bills_arr = $service->get_order($bill)) {
                $bills = $bills_arr;
            }
        }
        return $bills;
    }


}