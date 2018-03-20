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

class groupbooking_ctl_mobile_checkout extends b2c_mfrontpage
{
    public $title = '拼团订单确认';
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->verify_member();
        $this->set_tmpl('checkout');
    }
    //checkout 主页
    public function index($activity_id,$product_id,$quantity = 1,$main_id = null)
    {
        $blank_url = $this->gen_url(array(
            'app' => 'groupbooking',
            'ctl' => 'mobile_product',
            'act' => 'index',
            'args' => array($activity_id,$product_id)
        ));
        $sel_maddr_callback = $this->gen_url(array(
            'app' => 'groupbooking',
            'ctl' => 'mobile_checkout',
            'act' => 'index',
            'args'=>array($activity_id,$product_id)
        ));
        $time = time();
        if(!$product_id) {
            $this->splash('error',$blank_url,'未知商品');
        }
        if(!$activity = $this->app->model('activity')->getRow('*',array('activity_id'=>$activity_id,'start_time|sthan'=>$time,'end_time|than'=>$time))) {
            $this->splash('error', $blank_url, '未知拼团活动！');
        };
        $params = $this->_request->get_params(true);
        $check_params = array_merge($params,array(
            'main_id'=>$main_id,
            'activity'=>$activity,
            'member_id'=>$this->app->member_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
        ));
        if(!$this->app->model('orders')->check($check_params,$msg)) {
            $this->splash('error', $blank_url, $msg);
        };

        $this->pagedata = vmc::singleton('groupbooking_checkout_stage')->check($check_params);
        $this->pagedata['main_id'] = $main_id;
        $this->pagedata['sel_maddr_callback'] = $sel_maddr_callback;
        $this->page('mobile/checkout/index.html');
    }

    public function create()
    {
        $this->logger = vmc::singleton('b2c_order_log');
        $member_id = $this->app->member_id;
        $this->logger->set_operator(array(
            'ident' => $member_id,
            'name' => '会员',
            'model' => 'members',
        ));
        $params = utils::_filter_input($_POST);
        $params = array_merge($params,utils::decrypt($params['cart_md5']));
        $redirect_checkout = $this->gen_url(array(
            'app' => 'groupbooking',
            'ctl' => 'mobile_checkout',
            'args' => array(
                $params['activity_id'],
            ),
        ), true);
        $time = time();
        if(!$activity = $this->app->model('activity')->getRow('*',array('activity_id'=>$params['activity_id'],'start_time|sthan'=>$time,'end_time|than'=>$time))) {
            $this->splash('error', $redirect_checkout, '未知拼团活动！');
        };
        $params['activity'] = $activity;
        $params['member_id'] = $this->app->member_id;
        if(!$this->app->model('orders')->check($params,$msg)) {
            $this->splash('error', $redirect_checkout, $msg);
        };

        //新订单标准数据
        $order_sdf = array(
            'activity_id' => $params['activity_id'],
            'product_id' => $params['product_id'],
            'main_id' => $params['main_id'],
            'member_id' => $member_id,
            'pay_app' => $params['payapp_id'],
            'dlytype_id' => $params['dlytype_id'],
            'nums' => $params['quantity'],
            'createtime' => time() ,
            'platform' => 'mobile',
            'invoice_title' => $params['invoice_title'],
            'memo' => $params['memo'],
        );
        if ($order_sdf['need_shipping'] != 'N' && !$order_sdf['dlytype_id']) {
            $this->logger->fail('create', '未知配送方式', $params);
            $this->splash('error', $redirect_checkout, '未知配送方式');
        }
        //COD FIX
        if ($order_sdf['pay_app'] == '-1' || $order_sdf['pay_app'] == 'cod') {
            $order_sdf['is_cod'] = 'Y';
        } else {
            $dlytype = app::get('b2c')->model('dlytype')->dump($params['dlytype_id']);
            if ($dlytype['has_cod'] == 'true') {
                $order_sdf['pay_app'] = 'cod';
                $order_sdf['is_cod'] = 'Y';
            }
        }

        if (!$params['addr_id']) {
            $this->logger->fail('create', '无收货人信息', $params);
            $this->splash('error', $redirect_checkout, '无收货人信息');
        } else {
            $consignee = app::get('b2c')->model('member_addrs')->getRow('name,area,addr,zip,tel,mobile,email', array(
                'member_id' => $member_id,
                'addr_id' => $params['addr_id'],
            ));
            $order_sdf['consignee'] = $consignee;
        }
        if (!$order_sdf['pay_app']) {
            $this->logger->fail('create', '未知支付方式', $params);
            $this->splash('error', $redirect_checkout, '未知支付方式');
        }

        $db = vmc::database();
        //开启事务
        $this->transaction_status = $db->beginTransaction();
        $order_create_service = vmc::singleton('groupbooking_order_create');
        //&$order_sdf、&$msg
        if (!$order_create_service->generate($order_sdf,$activity, $msg)) {
            $db->rollback(); //事务回滚
            $msg = $msg ? $msg : '数据组织失败';
            $this->logger->fail('create', $msg, $params);
            $this->splash('error', $redirect_checkout, $msg);
        }
        if (!$order_create_service->save($order_sdf, $msg)) {
            $db->rollback(); //事务回滚
            $msg = $msg ? $msg : '数据保存失败';
            $this->logger->fail('create', $msg, $order_sdf);
            $this->splash('error', $redirect_checkout, $msg);
        }
        $db->commit($this->transaction_status); //事务提交
        $this->logger->success('create', '订单创建成功', $params);

        $redirect_payment = $this->gen_url(array(
            'app' => 'groupbooking',
            'ctl' => 'mobile_checkout',
            'act' => 'payment',
            'args' => array(
                $order_sdf['gb_id'],
                '1',
            ),
        ), true);

        $this->splash('success', $redirect_payment, '订单提交成功');
    }


    public function check($fastbuy = false)
    {
        $this->page('mobile/default.html');
    }


    public function payment($gb_id, $flow_success = 0 , $new_payappid)
    {
        $redirect = $this->gen_url(array(
            'app' => 'groupbooking',
            'ctl' => 'mobile_member',
            'act' => 'orders_list',
        ));
        $order = $this->app->model('orders')->dump($gb_id);
        if (!$order) {
            $this->splash('error', $redirect, '未知订单信息');
        }
        if ($this->app->member_id != $order['member_id']) {
            $this->splash('error', $redirect, '非法操作');
        }
        if ($order['status'] == 'dead') {
            $this->splash('error', $redirect, '订单已经取消，不能进行支付！');
        }
        if ($order['pay_status'] == '1' || $order['pay_status'] == '2' || $order['payed'] == $order['order_total']) {
            $this->splash('success', $redirect, '订单已付款！');
        }

        //变更支付方式
        if($new_payappid){
            if(!vmc::singleton('groupbooking_checkout_stage')->changepayment($gb_id,$new_payappid,$error_msg)){
                $this->pagedata['changepayment_errormsg'] = $error_msg;
            }else{
                //order pay_app is  updated
                $order['pay_app'] = $new_payappid;
            }
        }

        $mdl_payapps = app::get('ectools')->model('payment_applications');
        $filter = array(
            'status' => 'true',
            'platform_allow' => array(
                'mobile',
            ),
        );
        if(base_mobiledetect::is_hybirdapp()){
            unset($filter['platform_allow']);
            $filter['platform_allow'] = array(
                'app'
            );
        }
        if(base_component_request::is_wxapp()){
            unset($filter['platform_allow']);
            $filter['platform_allow'] = array(
                'wxapp'
            );
        }
        if ($order['is_cod'] == 'Y') {
            $filter['app_id'] = 'cod';
        }
        $payapps = $mdl_payapps->getList('*', $filter);
        $selected_payapp = $mdl_payapps->dump($order['pay_app']);
        $order['is_cod'] = "N";
        $this->pagedata['order'] = $order;
        $this->pagedata['payapps'] = $payapps;
        $this->pagedata['selected_payapp'] = $selected_payapp;
        $this->pagedata['flow_success'] = $flow_success;
        //$this->set_tmpl('checkout');
        $this->page('mobile/checkout/payment.html');
    }

    /**
     * 准备跳转到支付平台.
     *
     * @param mixed $gb_id 订单编号
     * @param $recursive 递归调用标记
     */
    public function dopayment($gb_id, $recursive = false)
    {

        $redirect = $this->gen_url(array(
            'app' => 'groupbooking',
            'ctl' => 'mobile_member',
            'act' => 'orders_list',
        ));
        $obj_bill = vmc::singleton('ectools_bill');
        $mdl_bills = app::get('ectools')->model('bills');
        $order = $this->app->model('orders')->dump($gb_id);
        if ($order['status'] == 'dead') {
            $this->splash('error', $redirect, '订单已经取消，不能进行支付！');
        }
        if ($order['pay_status'] == '1' || $order['pay_status'] == '2') {
            $this->splash('success', $redirect, '已支付');
        }
        if (in_array($order['pay_app'], array(
            'cod',
            'offline',
        ))) {
            $this->splash('error', $redirect, '不是在线支付方式');
        }
        if ($this->app->member_id != $order['member_id']) {
            $this->splash('error', $redirect, '非法操作');
        }

        //未交互过的账单复用
        $bill_sdf = array(
            'order_id' => $order['gb_id'],
            'bill_type' => 'payment',
            'pay_mode' => 'online',
            'pay_object' => 'gborder',
            'money' => $order['order_total'] - $order['payed'],
            'member_id' => $order['member_id'],
            'status' => 'ready',
            'pay_app_id' => $order['pay_app'],
            'memo' => $order['memo'],
            'app_id' =>'groupbooking'
        );
        $exist_bill = $mdl_bills->getRow('*',$bill_sdf);
        //一天内重复利用原支付单据
        if ($exist_bill && !empty($exist_bill['bill_id']) && $exist_bill['createtime']+86400>time()) {
            $bill_sdf = array_merge($exist_bill, $bill_sdf);
        } else {
            $bill_sdf['bill_id'] = $mdl_bills->apply_id($bill_sdf);
        }
        $bill_sdf['return_url'] = $this->gen_url(array(
            'app' => 'groupbooking',
            'ctl' => 'mobile_checkout',
            'act' => 'payresult',
            'args' => array(
                $bill_sdf['bill_id'],
            ),
        ));

        //微信内支付时，需要静默授权，以获得用户openid
        if (base_mobiledetect::is_wechat() && $order['pay_app'] == 'wxpay' && empty($bill_sdf['payer_account'])) {
            $wxpay_setting = unserialize(app::get('ectools')->getConf('wechat_payment_applications_wxpay'));
            $wxpay_appid = $wxpay_setting['appid'];
            $wxpay_appsecret = $wxpay_setting['appsecret'];
            $auth_code = $_GET['code'];
            $auth_state = $_GET['state'];
            if (!$recursive) {
                $oauth_redirect = $this->gen_url(array(
                    'app' => 'groupbooking',
                    'ctl' => 'mobile_checkout',
                    'act' => 'dopayment',
                    'args' => array($gb_id, 'recursive'),
                    'full' => 1,
                ));
                $oauth_redirect = urlencode($oauth_redirect);
                $oauth_action = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$wxpay_appid&redirect_uri=$oauth_redirect&response_type=code&scope=snsapi_base&state=$gb_id#wechat_redirect";
                logger::debug('微信snsapi_base URL:'.$oauth_action);
                $this->redirect($oauth_action); //静默授权
            } elseif ($recursive && $auth_code && $auth_state == $gb_id) {
                //把微信用户openid 记录到支付单据中
                $auth_token = vmc::singleton('base_httpclient')->get("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$wxpay_appid&secret=$wxpay_appsecret&code=$auth_code&grant_type=authorization_code");
                $auth_token = json_decode($auth_token, 1);
                if (!$auth_token['openid']) {
                    logger::warning('微信静默授权失败!'.var_export($auth_token, 1));
                    $this->splash('error', $redirect, '暂无法进行微信内支付。');
                }
                $bill_sdf['payer_account'] = $auth_token['openid'];
            } else {
                logger::warning('微信静默授权失败!id:'.$gb_id.'|'.var_export($_GET, 1));
            }
        }

        try {
            if (!$obj_bill->generate($bill_sdf, $msg)) {
                $this->splash('error', $redirect, $msg);
            }
        } catch (Exception $e) {
            $this->splash('error', $redirect, $e->getMessage());
        }
        $get_way_params = $bill_sdf;
        if (!vmc::singleton('ectools_payment_api')->redirect_getway($get_way_params, $msg)) {
            $this->splash('error', $redirect, $msg);
        }
        //here we go to the platform
    }

    /**
     * 监测订单支付状态.
     */
    public function paystatus($gb_id)
    {
        $mdl_orders = app::get('groupbooking')->model('orders');
        $order = $mdl_orders->getRow('member_id,pay_status', array('member_id' => $this->app->member_id, 'gb_id' => $gb_id));
        if ($order['member_id'] != $this->app->member_id) {
            $this->splash('error', '', '非法操作!');
        }
        switch ($order['pay_status']) {
            case '1':
            case '2':
            //case '3':
                $this->splash('success', '', '已支付');
                break;

            default:
                $this->splash('error', '', '未支付');
                break;
        }
    }

    //支付回调
    public function payresult($gb_id)
    {
        $mdl_bills = app::get('ectools')->model('bills');
        $order = $this->app->model('orders')->dump($gb_id);
        //未交互过的账单复用
        $bill_sdf = array(
            'order_id' => $order['gb_id'],
            'bill_type' => 'payment',
            'pay_mode' => 'online',
            'pay_object' => 'gborder',
            'member_id' => $order['member_id'],
            'pay_app_id' => $order['pay_app'],
            'memo' => $order['memo'],
            'app_id' =>'groupbooking'
        );
        $bill = $mdl_bills->getRow('*',$bill_sdf);
        if ($bill['member_id'] != $this->app->member_id) {
            $this->splash('error', $redirect, '非法操作');
        }
        $this->pagedata['bill'] = $bill;
        $this->pagedata['order'] = $order;
        //$this->set_tmpl('checkout');
        $this->page('mobile/checkout/payresult.html');
    }
}
