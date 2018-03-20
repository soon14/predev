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


class fastgroup_ctl_mobile_fastgroup extends mobile_controller
{
    public $title = '快团';
    public function __construct($app)
    {
        parent::__construct($app);
        vmc::singleton('base_session')->start();
    }
    public function subject($subject_id, $product_id)
    {
        if (!$subject_id) {
            vmc::singleton('mobile_router')->http_status(404);
        }
        $mdl_subject = $this->app->model('subject');
        $subject = $mdl_subject->dump($subject_id);
        if (!$subject || $subject['is_pub'] != 'true') {
            vmc::singleton('mobile_router')->http_status(404);
        }
        $this->title = $subject['fg_title'];
        $this->description = $subject['fg_intro'];
        if (!$product_id) {
            $pid = 'g'.$subject['goods_id'];
        } else {
            $pid = $product_id;
        }
        $goods_detail = vmc::singleton('b2c_goods_stage')->detail($pid, $msg); //引用传递
        $ecmath = vmc::singleton('ectools_math');
        $goods_detail['product']['finally_buy_price'] = $ecmath->number_minus(array(
            $goods_detail['product']['buy_price'],
            $subject['goods_discount']
        ));
        $this->pagedata['subject'] = $subject;
        $this->pagedata['goods_detail'] = $goods_detail;

        $mdl_payapps = app::get('ectools')->model('payment_applications');
        $filter = array(
             'status' => 'true',
             'platform_allow' => array(
                 'mobile',
             ),
         );
        $payapps = $mdl_payapps->getList('*', $filter);
        foreach ($payapps as $k => $value) {
            if (in_array($value['app_id'], array('offline', 'cod'))) {
                unset($payapps[$k]);
            }
        }
        $this->pagedata['payapps'] = $payapps;
        $this->page('mobile/subject/detail.html');
    }
    //下单并进行支付条转
    public function fastorder()
    {
        $this->begin();
        $params = $_POST;
        if (!$this->validate_order_fn($params, $error_msg)) {
            $this->splash('error', null, $error_msg);
        }
        //新订单标准数据
        $order_sdf = array(
            'member_id' => 0,//非会员 TODO
            'memo' => '快团订单|'.$params['customer_memo'],
            'pay_app' => $params['payapp_id'],
            'createtime' => time() ,
            'need_shipping' => 'N',
            'need_invoice' => 'false',
            'platform' => 'mobile',
            'consignee' => array(
                'mobile' => $params['mobile'],
            ),
        );
        $order_create_service = vmc::singleton('b2c_order_create');
        $order_logger = vmc::singleton('b2c_order_log');
        $cart_object = array(
            'goods' => array(
                'product_id' => $params['product_id'],
                'num' => $params['quantity'],
            ),
        );
        vmc::singleton('b2c_cart_stage')->add('goods', $cart_object, $error_msg, true);
        $cart_result = vmc::singleton('b2c_cart_stage')->result(array('is_fastbuy' => true));
        $ecmath = vmc::singleton('ectools_math');
        $mdl_subject = $this->app->model('subject');
        $subject = $mdl_subject->dump($params['subject_id']);

        //重新组织购物车数据  for 快团
        $cart_result['objects']['goods'][0]['item']['product']['buy_price'] = $ecmath->number_minus(array(
            $cart_result['objects']['goods'][0]['item']['product']['buy_price'],
            $subject['goods_discount'],
        ));
        $cart_result['goods_count'] = $params['quantity'];
        $cart_result['object_count'] = 1;
        $cart_result['weight'] = $ecmath->number_multiple(array(
            $cart_result['objects']['goods'][0]['item']['product']['weight'],
            $params['quantity'],
        ));
        $cart_result['cart_amount'] = $cart_result['finally_cart_amount'] = $ecmath->number_multiple(array(
            $cart_result['objects']['goods'][0]['item']['product']['buy_price'],
            $params['quantity'],
        ));
        unset($cart_result['objects']['goods'][0]['warning']);
        unset($cart_result['objects']['goods'][0]['disabled']);
        //vmc::dump($cart_result);exit;
        if (!$order_create_service->generate($order_sdf, $cart_result, $msg)) {
            $order_logger->fail('create', $msg, $params);
            $this->end(false, $msg);
        }

        if (!$order_create_service->save($order_sdf, $msg)) {
            $msg = $msg ? $msg : '数据保存失败';
            $order_logger->fail('create', $msg, $order_sdf);
            $this->end(false, $msg);
        }
        $order_logger->set_order_id($order_sdf['order_id']);
        $order_logger->success('create', '订单创建成功', $params);
        $mdl_fgorders = $this->app->model('fgorders');
        $new_fgorder = array(
            'skey' => $mdl_fgorders->gen_skey($order_sdf['order_id']),
            'order_id' => $order_sdf['order_id'],
            'mobile' => $params['mobile'],
            'subject_id' => $params['subject_id'],
            'customer_memo' => $params['customer_memo'],
            'createtime' => time(),
        );
        if ($mdl_fgorders->save($new_fgorder)) {
            $dopayment_redirect = $this->gen_url(array(
                'app' => 'fastgroup',
                'ctl' => 'mobile_fastgroup',
                'act' => 'dopayment',
                'args' => array($order_sdf['order_id']),
            ));
            $this->end_only();
            $db = vmc::database();
            $db->commit($this->transaction_status);
            $this->redirect($dopayment_redirect);
        } else {
            $this->end(false, '下单异常,请稍候重试');
        }
    }

    /**
     * 准备跳转到支付平台.
     *
     * @param mixed $order_id 订单编号
     * @param $recursive 递归调用标记
     */
    public function dopayment($order_id, $recursive = false)
    {
        if(empty($_SERVER['HTTP_REFERER'])&& !isset($_GET['code'])){
           die('no referer!');
        }
        $order_id_encrypt = utils::encrypt(array(
            'order_id' => $order_id,
        ));
        $redirect = $this->gen_url(array(
            'app' => 'fastgroup',
            'ctl' => 'mobile_fastgroup',
            'act' => 'order',
            'args' => array($order_id_encrypt),
        ));
        $obj_bill = vmc::singleton('ectools_bill');
        $mdl_bills = app::get('ectools')->model('bills');
        $order = app::get('b2c')->model('orders')->dump($order_id);

        if ($order['pay_status'] == '1' || $order['pay_status'] == '2') {
            $this->splash('success', $redirect, '已支付');
        }
        if (in_array($order['pay_app'], array(
            'cod',
            'offline',
        ))) {
            $this->splash('error', $redirect, '不是在线支付方式');
        }
        $ecmath = vmc::singleton('ectools_math');
        $money = $ecmath->number_minus(array(
            $order['order_total'],
            $order['payed']
        ));
        //未交互过的账单复用
        $bill_sdf = array(
            'order_id' => $order['order_id'],
            'bill_type' => 'payment',
            'pay_mode' => 'online',
            'pay_object' => 'order',
            'money' => $money,
            'member_id' => '0',
            'status' => 'ready',
            'pay_app_id' => $order['pay_app'],
            'pay_fee' => $order['cost_payment'],
            'memo' => '快团订单支付',
        );
        $exist_bill = $mdl_bills->getRow('*', $bill_sdf);
        //一天内重复利用原支付单据
        if ($exist_bill && !empty($exist_bill['bill_id']) && $exist_bill['createtime'] + 86400 > time()) {
            $bill_sdf = array_merge($exist_bill, $bill_sdf);
        } else {
            $bill_sdf['bill_id'] = $mdl_bills->apply_id($bill_sdf);
        }
        $bill_id_encrypt = utils::encrypt(array(
            'bill_id' => $bill_sdf['bill_id'],
        ));
        $bill_sdf['return_url'] = $this->gen_url(array(
            'app' => 'fastgroup',
            'ctl' => 'mobile_fastgroup',
            'act' => 'payresult',
            'args' => array(
                $bill_id_encrypt,
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
                    'app' => 'fastgroup',
                    'ctl' => 'mobile_fastgroup',
                    'act' => 'dopayment',
                    'args' => array($order_id, 'recursive'),
                    'full' => 1,
                ));
                $oauth_redirect = urlencode($oauth_redirect);
                $oauth_action = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$wxpay_appid&redirect_uri=$oauth_redirect&response_type=code&scope=snsapi_base&state=$order_id#wechat_redirect";
                logger::debug('微信snsapi_base URL:'.$oauth_action);
                $this->redirect($oauth_action); //静默授权
            } elseif ($recursive && $auth_code && $auth_state == $order_id) {
                //把微信用户openid 记录到支付单据中
                $auth_token = vmc::singleton('base_httpclient')->get("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$wxpay_appid&secret=$wxpay_appsecret&code=$auth_code&grant_type=authorization_code");
                $auth_token = json_decode($auth_token, 1);
                if (!$auth_token['openid']) {
                    logger::warning('微信静默授权失败!'.var_export($auth_token, 1));
                    $this->splash('error', $redirect, '暂无法进行微信内支付。');
                }
                $bill_sdf['payer_account'] = $auth_token['openid'];
            } else {
                logger::warning('微信静默授权失败!order_id:'.$order_id.'|'.var_export($_GET, 1));
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
      * 支付回调.
      */
     public function payresult($bill_id_encrypt)
     {
         $this->_response->set_header('Cache-Control', 'no-store');
         $this->title = '支付结果';
         $bill = utils::decrypt($bill_id_encrypt);
         $bill_id = $bill['bill_id'];
         if (!$bill_id) {
             $this->splash('error', null, '异常调用,未知的支付单号');
         }
         $mdl_bills = app::get('ectools')->model('bills');
         $bill = $mdl_bills->dump($bill_id);
         $this->pagedata['bill'] = $bill;
         $order_id_encrypt = utils::encrypt(array('order_id' => $bill['order_id']));
         $this->pagedata['order_id_encrypt'] = $order_id_encrypt;
         $this->page('mobile/fgorders/payresult.html');
     }

    /**
     * 支付方式切换.
     */
    public function changepayment($order_id_encrypt, $payapp_id)
    {
        $redirect = $this->gen_url(array(
            'app' => 'fastgroup',
            'ctl' => 'mobile_fastgroup',
            'act' => 'order',
            'args' => array(
                $order_id_encrypt,
            ),
        ));
        $order = utils::decrypt($order_id_encrypt);
        $order_id = $order['order_id'];
        if ($payapp_id) {
            if (!vmc::singleton('b2c_checkout_stage')->changepayment($order_id, $payapp_id, $error_msg)) {
                $this->splash('error', $redirect, $error_msg);
            } else {
                $update_arr = array(
                    'order_id' => $order_id,
                    'pay_app_id' => $payapp_id,
                );
                if (app::get('b2c')->model('orders')->save($update_arr)) {
                    $this->splash('success', $redirect, '支付方式切换成功');
                } else {
                    $this->splash('error', $redirect, '支付方式切换失败');
                }
            }
        } else {
            $this->splash('error', $redirect, '未知支付方式');
        }
    }

    //快捷下单前的输入验证
    public function validate_order()
    {
        if (!$this->validate_order_fn($_POST, $msg)) {
            $this->splash('error', null, $msg);
        } else {
            $this->splash('success', null, '验证成功');
        }
    }

    private function validate_order_fn($params, &$msg)
    {
        if (!$params['subject_id']) {
            $msg = '稍候再试';

            return false;
        }
        if (!$params['payapp_id']) {
            $msg = '请选择支付方式';

            return false;
        }
        if (!$params['mobile']) {
            $msg = '请填写手机号';

            return false;
        }
        if (!$params['vcode']) {
            $msg = '请填写手机验证码';

            return false;
        }

        if (!$params['quantity'] ||!is_int(intval($params['quantity'])) ||$params['quantity']<1) {
            $msg = '请合法的购买量！';
            return false;
        }
        $mdl_subject = $this->app->model('subject');
        $subject = $mdl_subject->dump($_POST['subject_id']);
        if (!$subject) {
            $msg = '稍候再试';

            return false;
        }
        if ($subject['is_pub'] != 'true') {
            $msg = '活动已关闭';

            return false;
        }
        if (time() < $subject['begin_time'] || time() > $subject['end_time']) {
            $msg = '活动未开始或已经结束';

            return false;
        }
        if (!vmc::singleton('b2c_goods_stock')->is_available_stock($params['product_bn'], $params['quantity'], $abs_stock)) {
            $msg = '库存不足';

            return false;
        }
        $order_quantity = $this->app->model('fgorders')->get_quantity_bysubject($params['subject_id']);
        if ($subject['limit'] > 0 && $subject['limit'] < $order_quantity['quantity'] +$params['quantity']) {
            $msg = '可订购数量不足';

            return false;
        }
        $per_order_quantity = $this->app->model('fgorders')->get_quantity_bymobile($params['subject_id'], $params['mobile']);

        if ($subject['per_limit'] > 0 && $subject['per_limit'] < $per_order_quantity['quantity'] +$params['quantity']) {
            $msg = '超出可订购数量,每人限购'.$subject['per_limit'];

            return false;
        }
        if ($params['mobile'] != $_SESSION['fastgroup']['validate_mobile']) {
            if (!vmc::singleton('b2c_user_vcode')->verify($params['vcode'], $params['mobile'], 'activation')) {
                $msg = '手机验证码错误';

                return false;
            } else {
                //会话中将手机号加入信任
                $_SESSION['fastgroup']['validate_mobile'] = $params['mobile'];
            }
        }

        return true;
    }

    public function order($order_id_encrypt)
    {
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->pagedata['order_id_encrypt'] = $order_id_encrypt;
        $order = utils::decrypt($order_id_encrypt);
        $order_id = $order['order_id'];
        $mdl_order = app::get('b2c')->model('orders');
        $this->pagedata['order'] = $order = $mdl_order->dump($order_id, '*', array(
            'items' => array(
                '*',
            ),
            'promotions' => array(
                '*',
            ),
            ':dlytype' => array(
                '*',
            ),
        ));
        $mdl_fgorders = $this->app->model('fgorders');
        $mdl_subject = $this->app->model('subject');

        $fgorder = $mdl_fgorders->getRow('*', array('order_id' => $order_id));
        $subject = $mdl_subject->dump($fgorder['subject_id']);
        $this->pagedata['fgorder'] = $fgorder;
        $this->pagedata['subject'] = $subject;
        $this->pagedata['payapp'] = app::get('ectools')->model('payment_applications')->dump($order['pay_app']);

        $mdl_payapps = app::get('ectools')->model('payment_applications');
        $filter = array(
             'status' => 'true',
             'platform_allow' => array(
                 'mobile',
             ),
         );
        $payapps = $mdl_payapps->getList('*', $filter);
        foreach ($payapps as $k => $value) {
            if (in_array($value['app_id'], array('offline', 'cod'))) {
                unset($payapps[$k]);
            }
        }
        $this->pagedata['payapps'] = $payapps;

        $this->title = '订单详情';
        $this->page('mobile/fgorders/detail.html');
    }

    public function cancel_order($order_id_encrypt)
    {
        $this->_response->set_header('Cache-Control', 'no-store');
        $redirect = $this->gen_url(array(
            'app' => 'fastgroup',
            'ctl' => 'mobile_fastgroup',
            'act' => 'order_list',
        ));
        $this->begin($redirect);
        $order = utils::decrypt($order_id_encrypt);
        $order_id = $order['order_id'];
        $sdf['order_id'] = $order_id;
        if (vmc::singleton('b2c_order_cancel')->generate($sdf, $msg)) {
            $this->end(true, ('订单取消成功！'));
        } else {
            $this->end(false, ('订单取失败！'.$msg));
        }
    }

    //根据手机号、验证码查看快团订单列表
    public function order_list($action = 'show', $page = 1)
    {
        $this->_response->set_header('Cache-Control', 'no-store');
        switch ($action) {
            case 'show':

                    $validate_mobile = $_SESSION['fastgroup']['validate_mobile'];

                    if (!$validate_mobile) {
                        return $this->order_list('validate_mobile');
                    }
                    $this->title = '订购历史';
                    $limit = 5;
                    $mdl_fgorders = $this->app->model('fgorders');
                    $filter = array('mobile' => $validate_mobile);
                    $fgorder_list = $mdl_fgorders->getList('*', $filter, ($page - 1) * $limit, $limit);
                    foreach ($fgorder_list as &$order) {
                        $order['order_id_encrypt'] = utils::encrypt(array(
                            'order_id' => $order['order_id'],
                        ));
                    }
                    $mdl_subject = $this->app->model('subject');
                    $subject_ids = array_keys(utils::array_change_key($fgorder_list, 'subject_id'));
                    $subject_arr = $mdl_subject->getList('*', array('id' => $subject_ids));
                    $subject_arr = utils::array_change_key($subject_arr, 'id');
                    $this->pagedata['subjects'] = $subject_arr;
                    $fgorder_count = $mdl_fgorders->count($filter);
                    $this->pagedata['fgorder_list'] = $fgorder_list;
                    $this->pagedata['fgorder_count'] = $fgorder_count;
                    $this->pagedata['mobile'] = $validate_mobile;
                    $this->pagedata['pager'] = array(
                        'total' => ceil($fgorder_count / $limit) ,
                        'current' => $page,
                        'link' => array(
                            'app' => 'fastgroup',
                            'ctl' => 'mobile_fastgroup',
                            'act' => 'order_list',
                            'args' => array(
                                'show',
                                ($token = time()),
                            ) ,
                        ) ,
                        'token' => $token,
                    );
                    $this->page('mobile/fgorders/list.html');
                break;
            case 'validate_mobile':
                    $this->title = '验证身份';
                    $params = $_POST;
                    if ($params['mobile']) {
                        if (!vmc::singleton('b2c_user_vcode')->verify($params['vcode'], $params['mobile'], 'activation')) {
                            $msg = '手机验证码错误';
                            $this->splash('error', null, $msg);
                        } else {
                            $_SESSION['fastgroup']['validate_mobile'] = $params['mobile'];
                            $redirect = $this->gen_url(array(
                                'app' => 'fastgroup',
                                'ctl' => 'mobile_fastgroup',
                                'act' => 'order_list',
                                'args' => array('show', 1),
                            ));
                            $this->splash('success', $redirect, '验证成功,正在进入订单列表...');
                        }
                    } else {
                        $this->page('mobile/fgorders/validate_mobile.html');
                    }
                break;
        }
    }

     /**
      * 发短信验证码.
      */
     public function get_vcode()
     {
         $this->_response->set_header('Cache-Control', 'no-store');
         $type = 'activation';
         $mobile = trim($_POST['mobile']);
         if (!preg_match('/^1[34578]{1}[0-9]{9}$/', $mobile)) {
             $this->splash('error', null, '请输入正确的手机号码', true);
         }
         $uvcode_obj = vmc::singleton('b2c_user_vcode');
         $vcode = $uvcode_obj->set_vcode($mobile, $type, $msg);
         if ($vcode) {
             //发送验证码 发送短信
             $data['vcode'] = $vcode;
             if (!$uvcode_obj->send_sms($type, (string) $mobile, $data)) {
                 $this->splash('error', null, '短信发送失败');
             }
         } else {
             $this->splash('failed', null, $msg);
         }
         $this->splash('success', null, '短信已发送');
     }
}
