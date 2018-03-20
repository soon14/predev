<?php
/**
 * Created by PhpStorm.
 * User: wl
 * Date: 2016/3/9
 * Time: 16:14
 */
class store_ctl_admin_history extends store_ctl_admin_controller
{

    public function __construct($app)
    {
        parent::__construct($app);
        $this->obj_pos_cash = vmc::singleton('store_pos_cash');
        $this ->store_id = $_SESSION['now_selected_store'];
        $this ->is_super = vmc::singleton('desktop_user') ->is_super();
        if(!$this ->store_id){
            exit("请先选择收银台");
        }
    }

    public function index(){
        $this->pagedata['storeInfo'] = $this ->app ->model('store')->getRow("*" ,array('store_id' =>$this ->store_id));
        $this ->pagedata['user'] = $this ->user->user_data;
        if(false == $this ->user ->is_super()){
            $this ->pagedata['base_order_filter'] =array(
                'store_id' => $this ->store_id,
                'op_id' => $this ->user ->get_id()
            );
        }
        $this->singlepage('admin/history/index.html', '退货退款台');
    }

    public function get_order_by_barcode(){
        $order_id = $_POST['filter'];
        $order = $this ->get_order_info($order_id);
        if($order){
            if(!$this ->is_super && ($order['store_id'] != $this ->store_id)){
                $this ->splash('error' ,'' ,'您没有权限查看该订单');
            }
            $order['all_total'] = $order['finally_cart_amount']+$order['memberlv_discount']+$order['pmt_goods']+$order['pmt_order'];
            $order['pmt_total'] = $order['pmt_goods']+$order['pmt_order'];
            $order['need_pay'] = $order['order_total'] -$order['payed'];
            $order['items'] = app::get('b2c') ->model('order_items')->getList('*' ,array('order_id' => $order_id));
            $this ->splash('success' ,'' ,$order);
        }
        $this ->splash('error' ,'' ,'没有查询到该订单');
    }

    public function item_edit()
    {
        $product_id = $_GET['product_id'];
        $mdl_product = app::get('b2c')->model('products');
        $filter = array(
            'product_id' => $product_id,
        );
        $product = $mdl_product->getRow('*', $filter);
        $this->pagedata['product'] = $product;

        $this->display('admin/history/item_edit.html');
    }

    public function reship(){
        $order_id = $_POST['order_id']; //退货订单
        $this->begin();

        $dlycorp_id = $_POST['dlycorp_id']?$_POST['dlycorp_id']:0; //物流公司
        $logistics_no = $_POST['logistics_no']?$_POST['dp_id']:0; //物流单号--门店
        $send_arr = $_POST['products']; // array('$order_item_id'=>$sendnum);
        if(!$send_arr){
            $this ->splash('error' ,'' ,'请先选择需要退货的商品数量');
        }

        $reship_nums =0;
        foreach($send_arr as $v){
            $reship_nums +=$v;
        }
        if(!$reship_nums){
            $this ->splash('error' ,'' ,'退货数量不正确');
        }
        $order = $this ->get_order_info($order_id);
        if(!$order){
            $this ->splash('error' ,'' ,'错误的订单号');
        }
        if(!$this ->is_super && ($order['store_id'] != $this ->store_id)){
            $this ->splash('error' ,'' ,'您没有权限查看该订单');
        }

        $delivery_sdf = array(
            'order_id' => $order_id,
            'delivery_type' => 'reship', //退货
            'member_id' => $order['member_id'],
            'op_id' => $this->user->user_id,
            'dlycorp_id' => $dlycorp_id, //实际选择的物流公司
            'logistics_no' => $logistics_no,
            'cost_freight' => $_POST['cost_freight']?$_POST['cost_freight']:0,
            'status' => 'ready',
            'memo' => '',
        );
        $obj_delivery = vmc::singleton('b2c_order_delivery');
        if (!$obj_delivery->generate($delivery_sdf, $send_arr, $msg) || !$obj_delivery->save($delivery_sdf, $msg)) {
            $this->end(false, $msg);
        }
        //计算理论上的应退款
        $refund_amount = 0;
        $sql = "SELECT A.* ,B.sendnum AS delivery_sendnum FROM vmc_b2c_order_items AS A, vmc_b2c_delivery_items AS B
WHERE A.item_id = B.order_item_id AND B.delivery_id = {$delivery_sdf['delivery_id']}";
        $db = vmc::database();
        $reship_items = $db ->select($sql);
        foreach($reship_items as $vv){
            $refund_amount += $vv['delivery_sendnum']*$vv['buy_price'];
        }
        $this->end(true, '退货成功！', null, array('delivery_id' => $delivery_sdf['delivery_id'] ,'refund_amount'=>$refund_amount));
    }


    public function show_refund_info(){
        $order_id = $_POST['order_id']; //退款订单
        $order = $this ->get_order_info($order_id);
        if(!$order){
            $this ->splash('error' ,'' ,'错误的订单号');
        }
        if(!$this ->is_super && ($order['store_id'] != $this ->store_id)){
            $this ->splash('error' ,'' ,'您没有权限操作该订单');
        }

        $this ->pagedata['order'] = $order;
        $this ->pagedata['refund_info'] = $this ->count_products_refund($order_id);
        $this ->pagedata['pay_method'] = app::get('ectools')->model('payment_applications')->dump($_POST['pay_app_id']);
        $this ->display('admin/history/refund_frame.html');
    }

    public function do_refund(){
        $order_id = $_POST['order_id']; //退款订单
        if((int)$_POST['money']<=0){
            $this ->splash('error' ,'' ,'退款金额不正确');
        }
        $order = $this ->get_order_info($order_id);
        if(!$order){
            $this ->splash('error' ,'' ,'错误的订单号');
        }
        if(!$this ->is_super && ($order['store_id'] != $this ->store_id)){
            $this ->splash('error' ,'' ,'您没有权限操作该订单');
        }
        $refund_info = $this ->count_products_refund($order_id);
        if($_POST['money'] > $refund_info['need_refund']){
            $this ->splash('error' ,'' ,'退款金额超出');
        }
        $this->begin();
        //账单生产类
        $obj_bill = vmc::singleton('ectools_bill');
        $bill_sdf = array(
            'order_id' =>$order_id,
            'bill_type' => 'refund',
            'pay_object' => 'order',
            'member_id' => $order['member_id'],
            'op_id' => $this->user->user_id,
            'status' => 'succ',
            'money' => $_POST['money'],
            'out_trade_no' => $_POST['out_trade_no'],
            'pay_app_id' => $_POST['pay_app_id'],
            'memo' =>'店铺收银员退款'
        );

        if (!$obj_bill->generate($bill_sdf, $msg)) {
            $this->end(false, $msg);
        } else {
            //退积分，即新建负积分记录对冲
            $return_score = $_POST['return_score'] ?$_POST['return_score'] : ceil(($_POST['money']/$order['order_total'])*$order['score_g']);
            vmc::singleton('b2c_member_integral')->change(array(
                'member_id' => $order['member_id'],
                'order_id' => $order_id,
                'change' => $return_score * -1,
                'change_reason' => 'refund',
                'op_model' => 'store_op',
                'op_id' => $this->user->user_id,
            ), $msg);
        }
        $this->end('true', '退款成功!', null, array('bill_id' => $bill_sdf['bill_id']));
    }

    //计算可退的最大金额
    private function count_products_refund($order_id){
        $db = vmc::database();
        $sql_payment = "SELECT SUM(money) as amount FROM `vmc_ectools_bills` WHERE order_id='$order_id' AND bill_type='payment' AND status='succ'";
        $sql_refund = "SELECT SUM(money) as amount  FROM `vmc_ectools_bills` WHERE order_id='$order_id' AND bill_type='refund' AND status='succ'";
        $pay_money = $db ->selectrow($sql_payment);
        $refund_money = $db ->selectrow($sql_refund);
        $products_refund = 0;
        $order_items = app::get('b2c')->model('order_items')->getList('*' ,array('order_id'=>$order_id));
        foreach($order_items as $v){
            $products_refund +=$v['buy_price']*($v['nums'] -$v['sendnum']);
        }
        $order_info = app::get('b2c')->model('orders')->getRow('*' ,array('order_id' =>$order_id));
        $refund_info =array(
            'pay_money' => $pay_money['amount'],
            'refund_money' => $refund_money['amount']? $refund_money['amount']:0,
            'products_refund' =>$products_refund,
            'need_refund' => ($products_refund - $refund_money['amount'])>$order_info['payed']?$order_info['payed']:($products_refund - $refund_money['amount'])
        );
        return $refund_info;
    }



    public function bill_list(){
        $order_id = $_POST['order_id']; //订单
        $order = $this ->get_order_info($order_id);
        if(!$order){
            $this ->splash('error' ,'' ,'错误的订单号');
        }
        if(!$this ->is_super && ($order['store_id'] != $this ->store_id)){
            $this ->splash('error' ,'' ,'您没有权限查看该订单');
        }
        $db = vmc::database();
        $sql = "SELECT A.*  ,B.op_no FROM vmc_ectools_bills AS A LEFT JOIN vmc_desktop_users AS B ON A.op_id=B.user_id
                WHERE A.order_id = '$order_id' AND A.status='succ'";
        $this ->pagedata['bill_list'] = $db ->select($sql);
        $this ->pagedata['pay_method'] = app::get('ectools')->model('payment_applications')->getList("*");
        $this ->pagedata['pay_method'] = utils::array_change_key($this ->pagedata['pay_method'] ,'app_id');
        $this ->display('admin/history/bill_list.html');
    }

    public function pmt_list(){
        $order_id = $_POST['order_id']; //订单
        $order = $this ->get_order_info($order_id);
        if(!$order){
            $this ->splash('error' ,'' ,'错误的订单号');
        }
        if(!$this ->is_super && ($order['store_id'] != $this ->store_id)){
            $this ->splash('error' ,'' ,'您没有权限查看该订单');
        }
        $this ->pagedata['promotions'] = app::get('b2c') ->model('order_pmt')->getList('*' ,array('order_id' => $order_id));
        $this ->display('admin/history/pmt_list.html');
    }

}