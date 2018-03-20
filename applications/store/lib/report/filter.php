<?php
/**
 * Created by PhpStorm.
 * User: wl
 * Date: 2016/2/3
 * Time: 20:19
 */
class store_report_filter{

    private function _get_title($key=''){
        $arr = array(
            'A.order_id' => "订单号",
            'A.order_total' => "订单金额",
            'A.pay_app' => "支付方式",
            'A.pay_status' => "支付状态",
            'A.ship_status' => "发货状态",
            'A.cost_freight' => "运费",
            'A.memberlv_discount' => "会员身份优惠金额",
            'A.pmt_goods' => "商品促销优惠金额",
            'A.pmt_order' => "订单促销优惠金额",
            'FROM_UNIXTIME(A.createtime)' => "下单时间",
            'C.store_bn' => "门店编号",
            'C.store_name' => "门店名称",
            'D.name' => '商品名称',
            'D.bn' => '商品编号',
            'D.buy_price' => '商品单价',
            'D.nums' => '商品数量',
            'D.amount' => '商品小计',
            'E.bill_id' => '交易单号',
            'E.bill_type' =>'交易类型',
            'E.status' => '支付状态',
            'E.money' =>'交易金额',
            'FROM_UNIXTIME(E.last_modify)' =>'交易时间',
            'E.out_trade_no' =>'支付平台流水号',
            'G.barcode' =>'条码',
            'nums' =>'商品数量',
            'amount' => '商品小计',
            "(CASE WHEN E.bill_type = 'payment' THEN E.money ELSE -E.money  END) AS money" => '交易金额',
            'U.op_no' => '收银员工号',
            'U.name' => '收银员姓名',
        );
        return $key?$arr[trim($key)] :$arr;
    }

    /*
     * 生成最终sql和条数
     */
    public function order_sql($filter){
        $where = " WHERE 1 AND A.order_id IN (select order_id from vmc_store_relation_orders) ";
        if($filter['report_type'] == 'order'){
            $field = "E.bill_id,E.bill_type,E.status,FROM_UNIXTIME(E.last_modify),
            (CASE WHEN E.bill_type = 'payment' THEN E.money ELSE -E.money  END) AS money,
            A.order_id, A.order_total, A.pay_app, A.ship_status, A.memberlv_discount, A.pmt_goods, A.pmt_order,C.store_name, E.out_trade_no,FROM_UNIXTIME(A.createtime),
            U.op_no,U.name ";
            $table_join = "vmc_b2c_orders AS A
LEFT JOIN vmc_store_relation_orders AS B ON A.order_id = B.order_id
LEFT JOIN vmc_store_store AS C ON B.store_id = C.store_id
RIGHT JOIN vmc_ectools_bills AS E ON A.order_id = E.order_id
LEFT JOIN vmc_desktop_users AS U ON U.user_id=E.op_id";

        }elseif($filter['report_type'] == 'order_items'){
            $field = "E.bill_id,E.bill_type,E.status,E.money,A.order_id, FROM_UNIXTIME(A.createtime) , A.order_total, A.pay_status, A.pay_app, A.cost_freight, C.store_bn, C.store_name, D.name, D.bn, D.buy_price,
            (CASE WHEN E.bill_type = 'payment' THEN D.nums ELSE -D.nums  END) AS nums,
            (CASE WHEN E.bill_type = 'payment' THEN D.amount ELSE -D.amount  END) AS amount,
            G.barcode ";
            $table_join = "(SELECT AA.order_id,AA.product_id, AA.name, AA.bn , AA.buy_price,BB.delivery_id ,BB.`sendnum` AS nums, AA.buy_price*BB.`sendnum` AS amount FROM vmc_b2c_order_items AA ,`vmc_b2c_delivery_items` AS BB
WHERE AA.`item_id` = BB.`order_item_id`) AS D
LEFT JOIN vmc_b2c_delivery AS H ON D.delivery_id =H.delivery_id
LEFT JOIN vmc_b2c_orders AS A ON D.order_id = A.order_id
LEFT JOIN vmc_store_relation_orders AS B ON A.order_id = B.order_id
LEFT JOIN vmc_store_store AS C ON B.store_id = C.store_id
LEFT JOIN vmc_ectools_bills AS E ON E.order_id = D.order_id
LEFT JOIN vmc_b2c_products AS G ON G.product_id = D.product_id
";
        }else{
            return false;
        }
        if($filter['from']){
            $where .=" AND E.last_modify>".strtotime($filter['from']);
        }
        if($filter['to']){
            $where .=" AND E.last_modify<=".strtotime($filter['to']);
        }
        if($filter['bill_type'] !='-1'){
            $where .=" AND E.bill_type='".$filter['bill_type']."'";
        }else{
            if($filter['report_type'] == 'order_items'){
                $where .=" AND E.`bill_type`=(CASE WHEN `delivery_type` = 'reship' THEN 'refund' ELSE 'payment' END)";
            }
        }
        if($filter['status'] !='-1'){
            $where .=" AND E.status='".$filter['status']."'";
        }
        if($filter['pay_status'] && $filter['pay_status'] !='-1'){
            $where .=" AND A.pay_status='".$filter['pay_status']."'";
        }
        if($filter['ship_status'] && $filter['ship_status'] !='-1'){
            $where .=" AND A.ship_status='".$filter['ship_status']."'";
        }
        if($filter['pmt'] =='0'){
            $where .=" AND (A.memberlv_discount = 0 AND A.pmt_goods = 0 AND A.pmt_order = 0)";
        }else if($filter['pmt'] =='1'){
            $where .=" AND (A.memberlv_discount <> 0 OR A.pmt_goods <> 0 OR A.pmt_order <> 0)";
        }
        if(!empty($filter['pay_app'])){
            $where .=" AND A.pay_app in ('" .implode("','" ,$filter['pay_app']) ."')";
        }
        if(!empty($filter['store_id'])){
            $where .=" AND C.store_id in ('" .implode("','" ,$filter['store_id']) ."')";
        }
        $orderby = "ORDER BY E.last_modify,A.order_id";

        $filed_arr = explode("," ,$field);
        $title = array();
        foreach($filed_arr as $v){
            $title[] = $this ->_get_title($v);
        }
        return array(
            'title' => $title,
            'field' => $field,
            'table' => $table_join,
            'where' => $where,
            'orderby' => $orderby,
            'type' =>$filter['report_type']
        );
    }

}
