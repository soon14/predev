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
class ubalance_ctl_mobile_member extends b2c_mfrontpage
{
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->verify_member();
        $this->set_tmpl('ubalance');
    }

    public function index($type = 'all', $page = 1)
    {
        $this->title = $this->app->getConf('name');
        $limit = 20;
        $filter = array('member_id' => $this->app->member_id);
        if ($type != 'all') {
            if ($type == 0) {
                //收入
                $filter['type'] = array('1' ,'3' ,'4' ,'7' ,'8');
            }
            if ($type == 1) {
                //支出
                $filter['type'] = array('2' ,'6');
            }
            if ($type == 2) {
                $filter['type'] = array('4');
            }
        }
        $mdl_record = app::get('ubalance')->model('fundlog');
        $data = app::get('ubalance')->model('set')->getRow('*');
        $balance_account = app::get('ubalance')->model('account')->getRow('*',
            array('member_id' => $this->app->member_id));
        //判断该用户是否开通账户密码
        //TODO
        // if ($balance_account['pay_password']) {
        //     $data['open_status'] = true;
        // }
        $data['user_balance'] = $balance_account['ubalance'];
        $data['income'] = $balance_account['income'];
        $income_record = $mdl_record->getRow('*', array(
            'member_id' => $this->app->member_id,
            'type' => '4',
            'opt_time|bthan' => strtotime('00:00:00'),
            'opt_time|lthan' => strtotime('+1 day 00:00:00'),
        ));
        $data['yesterday_income'] = $income_record['money'];
        $record = $mdl_record->getList('*', $filter, ($page - 1) * $limit, $limit);
        $record_count = $mdl_record->count($filter);

        $this->pagedata['data'] = $data;
        $this->pagedata['record_list'] = $record;
        $this->pagedata['pager'] = array(
            'total' => ceil($record_count / $limit),
            'current' => $page,
            'link' => array(
                'app' => 'ubalance',
                'ctl' => 'mobile_member',
                'act' => 'index',
                'args' => array(
                    ($token = time()),
                    'type' => $type,
                ),
            ),
            'token' => $token,
        );
        $fundlog_schema = $mdl_record->get_schema();
        $this->pagedata['type_list'] = $fundlog_schema['columns']['type']['type'];
        $this->pagedata['type'] = $type;
        $this->page('mobile/member/action/index.html');
    }

    public function password()
    {
        $user_obj = vmc::singleton('b2c_user_object');
        $this->pagedata['pam_data'] = $user_obj->get_pam_data('*', $this->app->member_id);
        $this->page('mobile/member/action/password.html');
    }

    public function recharge()
    {
        $this->title = $this->app->getConf('name').'充值';
        $setting = app::get('ubalance')->model('set')->getRow('*');
        $this->pagedata['setting'] = $setting;
        $balance_account = app::get('ubalance')->model('account')->getRow('*', array('member_id' => $this->app->member_id));
        $data['user_balance'] = $balance_account['ubalance'];
        $this->pagedata['data'] = $data;
        $this->pagedata['balance_account'] = $balance_account;
        $payapp_filter = array(
            'status' => 'true',
            'platform_allow' => array(
                'pc',
            ),
        );
        if ($params['payapp_filter']) {
            $payapp_filter = array_merge($payapp_filter, $params['payapp_filter']);
        }
        if (base_component_request::is_wxapp()) {
            $payapp_filter['platform_allow'] = array(
                'wxapp', //微信小程序
            );
        } elseif (base_mobiledetect::is_hybirdapp()) {
            $payapp_filter['platform_allow'] = array(
                'app', //hybirdapp
            );
        } elseif (base_mobiledetect::is_mobile()) {
            $payapp_filter['platform_allow'] = array(
                'mobile', //H5
            );
        }
        $mdl_payapps = app::get('ectools')->model('payment_applications');
        $paymentapps = $mdl_payapps->getList('*', $payapp_filter);
        foreach ($paymentapps as $key => $value) {
            if (in_array($value['app_id'], array('balance', 'offline', 'alipayguarantee', 'cod'))) {
                unset($paymentapps[$key]);
            }
        }
        $this->pagedata['payapps'] = $paymentapps;
        $this->page('mobile/member/action/recharge.html');
    }

    public function cash()
    {
        $this->title = $this->app->getConf('name').'提现';
        $member_id = $this->app->member_id;
        $setting = app::get('ubalance')->model('set')->getRow('*');
        $setting['exchange_ratio'] = (float)$setting['exchange_ratio'];
        $setting['cash_out_fee_ratio'] = (float)$setting['cash_out_fee_ratio'];
        $this->pagedata['setting'] = $setting;
        $balance_account = app::get('ubalance')->model('account')->getRow('*', array('member_id' => $this->app->member_id));
        $this->pagedata['account'] = $balance_account;
        $member_account = app::get('pam')->model('members')->getRow('login_account', array('login_type' => 'mobile', 'member_id' => $member_id));
        if($member_account){
            $this->pagedata['member_mobile'] = $member_account['login_account'];
        }
        $this->page('mobile/default.html');
    }
    /**
     * 处理提现请求
     *
     *  @$_POST
     */
    public function cash_out()
    {
        $this->begin();
        if($this->app->getConf('cash_out_enabled')!='1'){
            $this->end(false,'系统暂未开通提现功能');
        }
        $params = utils::_filter_input($_POST);
        $cash_out = $params['cash_out'];
        if(!$cash_out || $cash_out<0){
            $this->end(false);
        }
        $member_id = $this->app->member_id;
        $balance_name = $this->app->getConf('name');
        $balance_account = app::get('ubalance')->model('account')->getRow('*', array('member_id' => $member_id));
        if (!$balance_account) {
            $this->end(false, '账户异常');
        }
        $exchange_ratio = $this->app->getConf('exchange_ratio', 1);
        $cash_out_fee_ratio = $this->app->getConf('cash_out_fee_ratio', 0);
        $user_balance = $balance_account['ubalance'];
        $omath = vmc::singleton('ectools_math');
        $need_balance = $omath->number_multiple(array($cash_out, $exchange_ratio));
        $cash_out_fee_balance = $omath->number_multiple(array($need_balance,(float)$cash_out_fee_ratio));
        $cash_out_fee_money = $omath->number_div(array($cash_out_fee_balance,$exchange_ratio));
        $total_need_balance = $omath->number_plus(array($need_balance,$cash_out_fee));
        if($balance_account['status'] =='0'){
            $this->end(false, '余额账户已经禁用');
        }
        if($total_need_balance>$user_balance){
            $this->end(false,'余额不足');
        }
        //需手机验证
        $member_account = app::get('pam')->model('members')->getRow('login_account', array('login_type' => 'mobile', 'member_id' => $member_id));
        if(!$member_account){
            $this->end(false, '未绑定手机,请绑定手机');
        }
        if(!vmc::singleton('b2c_user_vcode')->verify($params['vcode'], $member_account['login_account'], 'reset')){
            $this->end(false, '手机验证码错误');
        }
        $mdl_bills = app::get('ectools')->model('bills');
        $memo = 'CNY与'.$balance_name.'汇率:'.$exchange_ratio.';提现手续费率:'.$cash_out_fee_ratio.';提现额:'.$need_balance.';提现手续费:'.$cash_out_fee;
        $refund_bill = array(
            'bill_type'=>'refund',
            'pay_object'=>'cashout',
            'money'=>$cash_out,
            'member_id'=>$member_id,
            'payee_name'=>$params['bill']['payee_name'],
            'payee_account'=>$params['bill']['payee_account'],
            'payee_bank'=>$params['bill']['payee_bank'],
            'pay_mode'=>'offline',
            'pay_app_id'=>'offline',
            'createtime'=>time(),
            'memo'=>$memo,
            'pay_fee'=>$cash_out_fee_money
        );
        $refund_bill['bill_id'] = $mdl_bills->apply_id($refund_bill);
        if(!$mdl_bills->save($refund_bill)){
            $this->end(false,'提现请求失败,请稍后再试');
        }else{
            $log_data = array(
                'member_id' => $member_id,
                'change_fund' => -$total_need_balance,
                'frozen_fund' => 0,
                'type' => '6',//提现
                'opt_id' => $member_id,
                'opt_type' => 'member',
                'opt_time' => time(),
                'mark' => '提现操作',
                'bill_id' => $refund_bill['bill_id']
            );
            if (!vmc::singleton('ubalance_account')->fund_change($log_data, $msg)) {
                //余额扣除失败
                $this->end(false, $msg?$msg:'余额扣除失败');
            }
        }
        $this->end(true);
    }
    /**
     * 查看提现状态
     */
    public function cash_status($fundlog_id){
        $mdl_fundlog = $this->app->model('fundlog');
        $fundlog = $mdl_fundlog->getRow('*',array('member_id'=>$this->app->member_id));
        if(!$fundlog){
            $this->splash('error',null,'非法单据');
        }

        $mdl_bills = app::get('ectools')->model('bills');
        $bill_schema = $mdl_bills->get_schema();
        $bill_status_map = $bill_schema['columns']['status']['type'];
        $bill = $mdl_bills->dump($fundlog['bill_id']);
        $this->pagedata['fundlog'] = $fundlog;
        $this->pagedata['bill'] = $bill;
        $this->pagedata['bill_status_map'] = $bill_status_map;
        $this->page('mobile/default.html');
    }
}
