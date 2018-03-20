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
class ubalance_ctl_site_balancepay extends b2c_frontpage
{
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->verify_member();
        $this->member = vmc::singleton('b2c_user_object')->get_current_member();
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->set_tmpl('ubalance');
    }

    public function index($bill_crypt)
    {
        $this->title = $this->app->getConf('name').'支付';
        $bill_arr = utils::decrypt($bill_crypt);

        if ($bill_arr['member_id'] != $this->member['member_id']) {
            $this->splash('error', null, '异常支付单据');
        }
        $account = $this->app->model('account')->dump($this->member['member_id']);
        $member_account = app::get('pam')->model('members')->getRow('login_account', array('login_type' => 'mobile', 'member_id' => $this->member['member_id']));
        if ($member_account) {
            $this->pagedata['member_mobile'] = $member_account['login_account'];
        }
        $this->pagedata['account'] = $account;
        $this->pagedata['bill_arr'] = $bill_arr;
        $this->pagedata['setting'] = app::get('ubalance')->model('set')->getRow('*');
        $this->page('site/pay/pay.html');
    }

    public function do_payment()
    {
        $this->begin();
        $member_id = $this->member['member_id'];

        $mdl_bills = app::get('ectools')->model('bills');
        $bill_id = $_POST['bill_id'];
        $bill = $mdl_bills->dump($bill_id);
        if ($bill['member_id'] != $member_id) {
            $this->end(false, '异常支付请求');
        }
        if ($bill['status'] == 'succ' || $bill['status'] == 'cancel') {
            $this->end(false, '支付单据生成失败');
        }
        $omath = vmc::singleton('ectools_math');
        $need_balance = $omath->number_multiple(array($bill['money'], $this->app->getConf('exchange_ratio', 1)));
        $balance_account = app::get('ubalance')->model('account')->getRow('*', array('member_id' => $member_id));
        if (!$balance_account) {
            $this->end(false, '账户异常');
        }
        if($balance_account['status'] =='0'){
            $this->end(false, '余额账户已经禁用');
        }
        if ($need_balance > $balance_account['ubalance']) {
            $this->end(false, '账户余额不足');
        }
        $member_account = app::get('pam')->model('members')->getRow('login_account', array('login_type' => 'mobile', 'member_id' => $member_id));
        if ($this->app->getConf('larger_type')=='1' && $need_balance >= $this->app->getConf('larger_sum') && $member_account['login_account']){
            //需手机验证
            if(!vmc::singleton('b2c_user_vcode')->verify($_POST['vcode'], $member_account['login_account'], 'reset')){
                $this->end(false, '手机验证码错误');
            }
        }
        $log_data = array(
            'member_id' => $member_id,
            'change_fund' => -$need_balance,
            'frozen_fund' => 0,
            'type' => '2',
            'opt_id' => $member_id,
            'opt_type' => 'member',
            'opt_time' => time(),
            'mark' => '支付订单'.$bill['order_id'],
            'bill_id' => $bill_id,
            'extfield' => $bill['order_id'],
        );
        if (!vmc::singleton('ubalance_account')->fund_change($log_data, $msg)) {
            //余额扣除失败
            $this->end(false, $msg);
        } else {
            $bill['status'] = 'succ';
            if(!vmc::singleton('ectools_bill')->generate($bill,$error_msg)){
                $this->end(false, $msg);
            }

            if ($bill['return_url']) {
                if (preg_match('/^http([^:]*):\/\//', $bill['return_url'])) {
                    $return_url = $bill['return_url'];
                } else {
                    $return_url = strtolower(vmc::request()->get_schema().'://'.vmc::request()->get_host()).$bill['return_url'];
                }
            } else {
                $return_url = array('app' => 'b2c','ctl' => 'site_order','act' => 'detail','args' => array($bill['order_id']));
            }
            $this->end(true, '支付成功', $return_url);
        }
    }

    /**
     * 充值页面.
     */
    public function do_recharge($bill_id = '', $recursive)
    {
        $redirect = $this->gen_url(array(
            'app' => 'ubalance',
            'ctl' => 'site_member',
            'act' => 'recharge',
        ));
        //金额判断
        $balance_set = app::get('ubalance')->model('set')->getRow('*');
        if ($_POST['money'] < $balance_set['recharge_limit']['limit_minimum'] || $_POST['money'] > $balance_set['recharge_limit']['limit_maximum']) {
            $this->splash('error', $redirect, '充值金额不在范围内');
        }
        $obj_bill = vmc::singleton('ectools_bill');
        $mdl_bills = app::get('ectools')->model('bills');
        if ($bill_id) {
            $bill_sdf = $mdl_bills->getRow('*', array('bill_id' => $bill_id));
        } else {
            $bill_sdf = array(
                'subject' => $balance_set['name'].'充值',
                'bill_type' => 'payment',
                'pay_mode' => 'online',
                'app_id' => 'ubalance',
                'pay_object' => 'recharge',
                'money' => (float) $_POST['money'],
                'member_id' => $this->member['member_id'],
                'status' => 'ready',
                'pay_app_id' => $_POST['payapp_id'],
                'pay_fee' => null,
                'memo' => $balance_set['name'].'充值',
            );
            $exist_bill = $mdl_bills->getRow('*', $bill_sdf);
            //一天内重复利用原支付单据
            if ($exist_bill && !empty($exist_bill['bill_id']) && $exist_bill['createtime'] + 86400 > time()) {
                $bill_sdf = array_merge($exist_bill, $bill_sdf);
            } else {
                $bill_sdf['bill_id'] = $mdl_bills->apply_id($bill_sdf);
            }
            $bill_sdf['return_url'] = $this->gen_url(array(
                'app' => 'ubalance',
                'ctl' => 'site_balancepay',
                'act' => 'recharge_result',
                'args' => array(
                    $bill_sdf['bill_id'],
                ),
            ));
        }

        //微信内支付时，需要静默授权，以获得用户openid
        if (base_mobiledetect::is_wechat() && $_POST['payapp_id'] == 'wxpay' && empty($bill_sdf['payer_account'])) {
            $wxpay_setting = unserialize(app::get('ectools')->getConf('wechat_payment_applications_wxpay'));
            $wxpay_appid = $wxpay_setting['appid'];
            $wxpay_appsecret = $wxpay_setting['appsecret'];
            $auth_code = $_GET['code'];
            $auth_state = $_GET['state'];
            if (!$recursive) {
                $oauth_redirect = $this->gen_url(array(
                    'app' => 'ubalance',
                    'ctl' => 'site_balancepay',
                    'act' => 'do_recharge',
                    'args' => array($bill_id, 'recursive'),
                    'full' => 1,
                ));
                $oauth_redirect = urlencode($oauth_redirect);
                $oauth_action = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$wxpay_appid&redirect_uri=$oauth_redirect&response_type=code&scope=snsapi_base&state=$bill_id#wechat_redirect";
                logger::debug('微信snsapi_base URL:'.$oauth_action);
                $this->redirect($oauth_action); //静默授权
            } elseif ($recursive && $auth_code && $auth_state == $bill_id) {
                //把微信用户openid 记录到支付单据中
                $auth_token = vmc::singleton('base_httpclient')->get("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$wxpay_appid&secret=$wxpay_appsecret&code=$auth_code&grant_type=authorization_code");
                $auth_token = json_decode($auth_token, 1);
                if (!$auth_token['openid']) {
                    logger::warning('微信静默授权失败!'.var_export($auth_token, 1));
                    $this->splash('error', $redirect, '暂无法进行微信内支付。');
                }
                $bill_sdf['payer_account'] = $auth_token['openid'];
            } else {
                logger::warning('微信静默授权失败!bill_id:'.$bill_id.'|'.var_export($_GET, 1));
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

    //充值回调
    public function recharge_result($bill_id)
    {
        $this->title = '支付结果';
        $mdl_bills = app::get('ectools')->model('bills');
        $bill = $mdl_bills->dump($bill_id);
        if ($bill['member_id'] != $this->member['member_id']) {
            $this->splash('error', $redirect, '非法操作');
        }
        $this->pagedata['bill'] = $bill;
        $this->page('site/pay/result.html');
    }
}
