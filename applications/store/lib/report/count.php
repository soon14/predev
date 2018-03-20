<?php
/**
 * Created by PhpStorm.
 * User: wl
 * Date: 2016/2/4
 * Time: 11:58
 */
class store_report_count{

    public function get_count($type ,$store ,$user_id ,$filter = array() ,$all_user = false){
        $db = vmc::database();
        $where = " WHERE 1 AND A.order_id IN (select order_id from vmc_store_relation_orders) ";
        if($filter['report_from']){
            $where .=" AND E.last_modify>".strtotime($filter['report_from']);
        }
        if($filter['report_to']){
            $where .=" AND E.last_modify<=".strtotime($filter['report_to']);
        }
        $table_join = "vmc_b2c_orders AS A
LEFT JOIN vmc_store_relation_orders AS B ON A.order_id = B.order_id
LEFT JOIN vmc_store_store AS C ON B.store_id = C.store_id
RIGHT JOIN vmc_ectools_bills AS E ON A.order_id = E.order_id ";
        $user_where = $all_user ? "" :" AND E.op_id=$user_id";
        if($type == 'store'){
            $succ_where = $where. " AND E.bill_type='payment' AND C.store_id={$store} AND E.status='succ' {$user_where}";
            $refund_where = $where. " AND E.bill_type='refund' AND E.status='succ' AND C.store_id = {$store} {$user_where}";//已退货
        }elseif($type == 'center'){
            $store = implode(',' ,$store);
            $succ_where = $where. " AND E.bill_type='payment' AND C.store_id IN ({$store}) AND E.status='succ' {$user_where}";
            $refund_where = $where. " AND E.bill_type='refund' AND E.status='succ' AND C.store_id IN ({$store}) {$user_where}";//已退货
        }
        //总的收款
        $total_amount_sql = "SELECT sum(E.money) as total FROM {$table_join} {$succ_where}";
        $total_nums_sql = "SELECT COUNT(DISTINCT A.order_id) as total FROM {$table_join} {$succ_where}";
        $total_amount = $db ->select($total_amount_sql);
        $total_nums = $db ->select($total_nums_sql);
        //总的退款
        $refund_amount_sql = "SELECT sum(E.money) as total FROM {$table_join} {$refund_where}";
        $refund_nums_sql = "SELECT count(E.bill_id) as total FROM {$table_join} {$refund_where}";
        $refund_amount = $db ->select($refund_amount_sql);
        $refund_nums = $db ->select($refund_nums_sql);
        //以支付方式分组
        $amount_group_sql =  "SELECT E.pay_app_id as pay_app, count(E.bill_id) as nums, sum(E.money) as total FROM {$table_join} {$succ_where} GROUP BY E.pay_app_id";
        $amount_group = $db ->select($amount_group_sql);

        $refund_amount_group_sql =  "SELECT E.pay_app_id as pay_app, count(E.bill_id) as nums, sum(E.money) as total FROM {$table_join} {$refund_where} GROUP BY E.pay_app_id";
        $refund_amount_group = $db ->select($refund_amount_group_sql);

        //以店铺分组
        if($type == 'center'){
            $amount_store_sql = "SELECT C.store_bn, C.store_name, sum(E.money) as total FROM {$table_join} {$succ_where} GROUP BY C.store_id";
            $amount_store = $db ->select($amount_store_sql);
        }
        //未发货订单
        if($type== 'store'){
            $succ_where = "$succ_where AND A.order_id NOT IN (SELECT order_id FROM vmc_b2c_delivery WHERE delivery_type='send')";
            $no_send_sql = "SELECT COUNT(E.bill_id) AS nums ,SUM(E.money) AS total FROM {$table_join} {$succ_where}";
            $no_send = $db ->select($no_send_sql);
        }

        //数据处理
        return array(
            'total_amount' =>$total_amount[0]['total'] ?$total_amount[0]['total'] :0,
            'total_nums' =>$total_nums[0]['total'] ? $total_nums[0]['total'] :0,
            'refund_amount' =>$refund_amount[0]['total'] ? $refund_amount[0]['total']:0,
            'refund_nums' =>$refund_nums[0]['total'] ?$refund_nums[0]['total'] :0,
            'amount_group_pay' =>$amount_group,
            'refund_group_pay' =>$refund_amount_group,
            'amount_group_store' =>$amount_store,
            'no_send' => $no_send[0]
        );

    }
}
