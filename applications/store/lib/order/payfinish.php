<?php
class store_order_payfinish
{
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
        //非bbc订单
        if($bill['order_id']){
            //带入操作员id
            $op_id = vmc::singleton('desktop_user') ->get_id();
            if($op_id){
                app::get('ectools')->model('bills') ->update(array('op_id' => $op_id) ,array('bill_id' => $bill['bill_id']));
            }
            $order_id = $bill['order_id'];
            $mdl_orders = app::get('b2c')->model('orders');
            $subsdf = array(
                'items' => array(
                    '*',
                )
            );
            $order = $mdl_orders->dump($order_id, '*', $subsdf);
            $order_store = app::get('store') ->model('relation_orders') ->getRow('*' ,array('order_id'=>$order_id));
            if($order_store && $order['pay_status']=='1' && $order['need_shipping'] =='N') {//说明是门店订单
                $delivery_sdf = array(
                    'order_id' => $order_id,
                    'delivery_type' => 'send', //发货
                    'member_id' => $order['member_id'],
                    'op_id' => null,
                    'dlycorp_id' => null, //实际选择的物流公司
                    'logistics_no' => null,
                    'cost_freight' => $order['cost_freight'],
                    'consignor' => null,
                    'consignee' => $order['consignee'], //sdf array
                    'status' => 'ready',
                    'memo' => $_POST['memo'],
                );
                $send_arr = array();
                foreach($order['items'] as $item){
                    $send_arr[$item['item_id']] = $item['nums'];
                }
                $obj_delivery = vmc::singleton('b2c_order_delivery');
                if (!$obj_delivery->generate($delivery_sdf, $send_arr, $msg) || !$obj_delivery->save($delivery_sdf, $msg)) {
                    logger::error('自动发货出现异常!ORDER_ID:' . $order_id . ',' . $msg);
                }
            }
        }
        return true;
    }
}
