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
class ubalance_ctl_site_member extends b2c_frontpage
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
                'ctl' => 'site_member',
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
        $this->page('site/member/action/index.html');
    }

    public function password()
    {
        $user_obj = vmc::singleton('b2c_user_object');
        $this->pagedata['pam_data'] = $user_obj->get_pam_data('*', $this->app->member_id);
        $this->page('site/member/action/password.html');
    }

    public function recharge()
    {
        $this->title = $this->app->getConf('name').'充值';
        $setting = app::get('ubalance')->model('set')->getRow('*');
        $this->pagedata['setting'] = $setting;
        $balance_account = app::get('ubalance')->model('account')->getRow('*', array('member_id' => $this->app->member_id));
        $data['user_balance'] =  $balance_account['ubalance'];
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
        if(base_component_request::is_wxapp()){
            $payapp_filter['platform_allow'] =  array(
                'wxapp', //微信小程序
            );
        }elseif(base_mobiledetect::is_hybirdapp()){
            $payapp_filter['platform_allow'] =  array(
                'app', //hybirdapp
            );
        }elseif(base_mobiledetect::is_mobile()){
            $payapp_filter['platform_allow'] =  array(
                'mobile', //H5
            );
        }
        $mdl_payapps = app::get('ectools')->model('payment_applications');
        $paymentapps = $mdl_payapps->getList('*', $payapp_filter);
        foreach ($paymentapps as $key => $value) {
            if(in_array($value['app_id'],array('balance','offline','alipayguarantee','cod'))){
                unset($paymentapps[$key]);
            }
        }
        $this->pagedata['payapps'] = $paymentapps;
        $this->page('site/member/action/recharge.html');
    }
}
