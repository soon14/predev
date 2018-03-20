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


class store_ctl_admin_pay extends store_ctl_admin_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

    /**
     * 创建支付单
     */
    public function create_pay_bill()
    {
        $order_id = $_POST['order_id'];
        $pay_app_id = $_POST['pay_app_id'];
        $pay_bill_info = vmc::singleton('store_pay_bill')->create_payment_bill($order_id ,$pay_app_id);
        if($pay_bill_info){
            $this->splash('success', '', '支付单创建成功', '', array('pay_bill_info' => $pay_bill_info));
        }else{
            $this->splash('error', '', '支付单创建失败');
        }
    }

    /**
     * 现金支付打印支付凭据
     */
    public function print_cashier_credentials()
    {
        $order_id = $_GET['orderId'];
        $bill_id = $_GET['billId'];
        $pages = array();
        $this ->pagedata['pages']  =array_pad($pages ,$_GET['pages'] ? $_GET['pages'] :3 ,0) ;
        //查询订单信息
        $order_info = $this ->get_order_info($order_id);
        $this->pagedata['orderInfo'] = $order_info;
        $store_info =  app::get('store')->model('store')->dump($order_info['store_id']);
        $this->pagedata['orderStoreInfo'] = $store_info;
        //查询订单细单信息
        $order_items = app::get('b2c')->model('order_items')->getList('*', array('order_id' => $order_id));
        $this->pagedata['orderItemInfos'] = $order_items;

        //查询订单操作员信息
        $modelDesktopusers = app::get('desktop')->model('users');
        $orderOpInfo = $modelDesktopusers->dump($this ->user->user_id);
        $this->pagedata['orderOpInfo'] = $orderOpInfo;

        $this->display('admin/pay/cashier_credentials.html');
    }

    /**
     * 去支付
     */
    public function toPay()
    {
        $pay_data = $_POST['pay'];
        $obj_store_pay = vmc::singleton('store_order_pay');
        $this->begin();

        $pay_result = $obj_store_pay->store_pay($pay_data);
        if ($pay_result === false) {
            $pay_response = [
                'pay_response' => $obj_store_pay->get_pay_response()
            ];
            $this->end(false, $obj_store_pay->getMsg(), '', $pay_response);
        }

        $this->end(true, '支付成功', '', ['payment_info' => $obj_store_pay->get_pay_data()]);

    }

    /**
     * 检查支付结果
     */
    public function check_pay_result()
    {

        $pay_data = $_POST['pay'];
        $msg = '';

        //实例化店铺支付对象
        $obj_store_pay = new store_order_pay();

        $check_pay_result = $obj_store_pay->check_pay_result($pay_data, $msg);
        if ($check_pay_result === false) {

            $this->splash('error', '', $msg);
        }

        $this->splash('success', '', '支付成功');
    }

    /**
     * 现金支付打印收银小票
     */
    public function print_receipt()
    {
        $order_id = $_GET['orderId'];
        $bill_id = $_GET['billId'];
        $order_info = $this ->get_order_info($order_id);
        $this->pagedata['orderInfo'] = $order_info;
        $store_info = app::get('store')->model('store')->dump($order_info['store_id']);
        $this->pagedata['orderStoreInfo'] = $store_info;
        $order_items = app::get('b2c')->model('order_items')->getList('*', array('order_id' => $order_id));
        $this->pagedata['orderItemInfos'] = $order_items;

        //查询订单操作员信息
        $modelDesktopusers = app::get('desktop')->model('users');
        $orderOpInfo = $modelDesktopusers->dump($order_info['op_id']);
        $this->pagedata['orderOpInfo'] = $orderOpInfo;

        //查询出状态是succ全部的支付单号
        $paybillsInfos = app::get('ectools')->model('bills') ->getList('*' ,array('order_id' =>$order_id,'status' => 'succ','bill_type'=>'payment') ,0,-1,'last_modify DESC');
        $billOpInfo = $modelDesktopusers->dump($paybillsInfos[0]['op_id']);
        $this->pagedata['billOpInfo'] = $billOpInfo;
        $this ->pagedata['get'] = $_GET;

        $pay_app_list = app::get('ectools')->model('payment_applications')->getList("*");
        $pay_app_list = utils::array_change_key($pay_app_list ,'app_id');
        $data = array();
        foreach($paybillsInfos as $k => $v) {
            $data = array(
                'name' => $pay_app_list[$v['pay_app_id']]['name'],
                'cardNO' => '',
                'cardBalance' => '',
                'momey'=>$v['money'],
                'payCode'=>$v['pay_app_id']
            );
        }
        $this->pagedata['allbillsInfos'] = $data;
        $this->display('admin/pay/cash_receipt.html');
    }

    /**
     * ajax获取结账信息
     */
    public function show_payment_infos()
    {
        $order_id = $_POST['order_id'];
        $bill_id = $_POST['pay_bill_id'];
        if (!$bill_id) {
            $this->splash('error', '', '缺少支付单据');
        } else {
            //查询支付单信息
            $mdl_bills = app::get('ectools')->model('bills');
            $bill_info = $mdl_bills->dump($bill_id);
            if (!$bill_info) {
                $this->splash('error', '', '没有支付单的信息');
            } else {
                if ($bill_info['order_id'] != $order_id) {
                    $this->splash('error', '', '支付单和订单对应错误');
                }
            }
        }
        $this->pagedata['paybillId'] = $bill_id;
        $this->pagedata['paybillInfo'] = $bill_info;
        //查询订单信息
        $mdl_order = app::get('b2c')->model('orders');
        $order_info = $mdl_order->dump($order_id);
        if (!$order_info) {
            $this->splash('error', '', '订单信息错误,请确认');
        }
        if ($order_info['pay_status'] == '1' || $order_info['pay_status'] == '2') {

            $this->splash('error', '', '这个订单已经支付过了');
        }
        $this->pagedata['orderInfo'] = $order_info;

        //查询订单支付方式信息
        $pay_app_Info = app::get('ectools')->model('payment_applications')->dump($order_info['pay_app']);
        $this->pagedata['orderPaymethodInfo'] = $pay_app_Info;

        $splash_params = [
            'order_info' => $order_info,
            'pay_body' => $this->fetch('admin/pay/pay_frame.html')
        ];

        $this->splash('success', '', '', '', $splash_params);
    }
}
