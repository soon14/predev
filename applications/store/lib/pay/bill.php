<?php
/**
 * Created by PhpStorm.
 * User: wl
 * Date: 2016/3/29
 * Time: 16:37
 */
class store_pay_bill{


    public function create_payment_bill($order_id ,$pay_app_id=''){
        $mdl_orders = app::get('b2c')->model('orders');
        $order_info = $mdl_orders->dump($order_id);
        $pay_app_id = $pay_app_id ?$pay_app_id :$order_info['pay_app'];
        $user = vmc::singleton('desktop_user');
        //构造支付单数据
        $bill_data = array(
            'order_id'   => $order_info['order_id'],
            'bill_type'  => 'payment',
            'pay_mode'   => 'online',
            'pay_object' => 'order',
            'money'      => $order_info['order_total'] - $order_info['payed'],
            'member_id'  => $order_info['member_id'],
            'status'     => 'ready',
            'pay_app_id' => $pay_app_id,
            'pay_fee'    => $order_info['cost_payment'],
            'memo'       => '门店支付单据',
            'op_id'       => $user->get_id(),
        );

        $mdl_bills = app::get('ectools')->model('bills');
        $exist_bill = $mdl_bills->getRow('*', array('order_id' =>$order_info['order_id'],'status' => 'ready','pay_app_id'=>$pay_app_id,'op_id'=>$user->get_id()));
        //一天内重复利用原支付单据
        if (is_array($exist_bill) && $exist_bill['createtime'] + 86400 > time()) {
            return  array_merge($exist_bill, $bill_data);
        }
        //生成唯一的支付单id
        $bill_data['bill_id'] = $mdl_bills->apply_id($bill_data);
        $bill_data['return_url'] = '';
        $obj_bill = vmc::singleton('ectools_bill');
        //创建支付单
        if (!$obj_bill->generate($bill_data, $msg)) {
            logger::error('门店支付单创建失败'.$msg.'data：'.var_export($bill_data ,1));
            return false;
        }
        if($pay_app_id != $order_info['pay_app']){
            $mdl_orders ->update(array('pay_app'=> $pay_app_id) ,array('order_id' =>$order_id));
            vmc::singleton('b2c_checkout_stage')->changepayment($order_id, $pay_app_id, $msg);
        }
        return $bill_data;
    }
}