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


class experiencestore_ctl_mobile_activity extends mobile_controller
{
    public $title = '活动';
    public function __construct($app)
    {
        parent::__construct($app);
        $this->set_tmpl('experiencestore_activity');
    }
    public function subject($store_id)
    {
        $store_list = $this->app->model('store')->getList();
        $this->pagedata['store_list'] = $store_list;
        if ($store_id) {
            $store_id = $store_list[0]['id'];
        }
        $mdl_schedule = $this->app->model('activity_schedule');
        $subject_id = $mdl_schedule->getColumn('subject_id', array('store_id' => $store_id, 'is_pub' => 'true'));
        $mdl_subject = $this->app->model('activity_subject');
        //TODO 分页
        $subjects = $mdl_subject->getList('*', array('id' => $subject_id));
        $this->pagedata['subjects'] = $subjects;
        $this->pagedata['select_store'] = $this->app->model('store')->dump($store_id);
        $this->page('mobile/activity/subject.html');
    }

    public function schedule($store_id, $subject_id)
    {
        header('cache-control: no-store, no-cache, must-revalidate');
        $this->pagedata['store_id'] = $store_id;
        $this->pagedata['subject_id'] = $subject_id;
        $mdl_schedule = $this->app->model('activity_schedule');
        $mdl_ticket = $this->app->model('activity_ticket');
        $current_time = time();
        $schedule_base_filter = array(
            'begin_time|lthan' => $current_time,
            'end_time|than' => $current_time,
            'from_time|than' => $current_time,
        );
        $filter = array_merge($schedule_base_filter, array('store_id' => $store_id, 'subject_id' => $subject_id));
        $schedule_list = $mdl_schedule->getList('*', $filter);
        $subject = $this->app->model('activity_subject')->dump($subject_id);
        if (base_component_request::is_wxapp()) {
            $subject['desc'] = $this->html_filter($subject['desc']);
        }
        $this->pagedata['subject'] = $subject;
        $schedule_list_group = array();
        foreach ($schedule_list as $key => $value) {
            $value['tickets'] = $mdl_ticket->getList('*', array('schedule_id' => $value['id']));
            $group_key = date('Ymd', $value['from_time']);
            $group_key .= '-'.date('w', $value['from_time']);
            $value['from_time_fmt'] = date('H:i', $value['from_time']);
            $value['to_time_fmt'] = date('m月d日 H:i', $value['to_time']);
            $schedule_list_group[$group_key][] = $value;
        }
        $this->pagedata['schedule_list_group'] = $schedule_list_group;
        $this->pagedata['store'] = $this->app->model('store')->dump($store_id);
        $this->page('mobile/activity/schedule.html');
    }

    public function order()
    {
        $member_id = vmc::singleton('b2c_user_object')->get_member_id();
        if (!$member_id) {
            $this->splash('error', $this->gen_url(array(
                'app' => 'b2c',
                'ctl' => 'mobile_passport',
                'act' => 'login',
            )), '未登录');
        }
        $this->begin();
        $params = utils::_filter_input($_POST);
        $store_id = $params['store_id'];
        $subject_id = $params['subject_id'];
        $schedule_id = $params['schedule_id'];
        $ticket_id = $params['ticket_id'];
        $p_order = $params['order'];
        $mdl_schedule = $this->app->model('activity_schedule');
        $mdl_order = $this->app->model('activity_order');
        $schedule = $mdl_schedule->dump($schedule_id);
        if (!$schedule) {
            $this->end(false, '预约失败,不存在的活动场次');
        }
        if ($exist_order = $mdl_order->getRow('id', array('schedule_id' => $schedule_id))) {
            // $this->end(true, '重复的预约',
            // array('app'=>'experiencestore',
            // 'ctl'=>'mobile_activity',
            // 'act'=>'order_success',
            // 'args'=>array($exist_order['id'])));
        }
        if ($schedule['from_time'] < time()) {
            //    $this->end(false, '预约失败,该场次活动已开场');
        }

        $mdl_subject = $this->app->model('activity_subject');
        $mdl_ticket = $this->app->model('activity_ticket');
        $ticket = $mdl_ticket->getRow('*', array(
            'schedule_id' => $schedule_id,
            'id' => $ticket_id,
        ));
        if ($p_order['order_id']) {
            //编辑预约
            $exist_order = $mdl_order->dump($p_order['order_id']);
            if (!$exist_order) {
                $this->end(false, '编辑失败!参数错误');
            }
            $exist_order['schedule_id'] = $schedule_id;

            $exist_order['need_ticket'] = is_array($ticket) ? true : false;
            if ($exist_order['need_ticket']) {
                $exist_order['ticket_name'] = $ticket['name'];
                $exist_order['ticket_batch_no'] = $ticket['batch_no'];
                $exist_order['ticket_id'] = $ticket_id;
                $exist_order['ticket_price'] = $ticket['price'];
            } else {
                $exist_order['ticket_name'] = '';
                $exist_order['ticket_batch_no'] = '';
                $exist_order['ticket_price'] = 0;
                $exist_order['ticket_id'] = 0;
            }
            if (!$mdl_order->save($exist_order)) {
                $this->end(false, '修改预约失败!');
            }
            //TODO 差异票价
            $order_success_url = $this->gen_url(array(
                'app' => 'experiencestore',
                'ctl' => 'mobile_activity',
                'act' => 'order_success',
                'args' => array($exist_order['id']),
            ));

            $this->end(true, '报名预约成功', $order_success_url);
        } else {
            //新预约
            $new_order = array(
                'id' => $mdl_order->apply_id(),
                'member_id' => $member_id,
                'store_id' => $store_id,
                'subject_id' => $subject_id,
                'schedule_id' => $schedule_id,
                'need_ticket' => is_array($ticket) ? true : false,
                'payed' => 0,
                'createtime' => time(),
            );
            $new_order = array_merge($p_order, $new_order);

            if ($new_order['need_ticket']) {
                $new_order['ticket_name'] = $ticket['name'];
                $new_order['ticket_batch_no'] = $ticket['batch_no'];
                $new_order['ticket_price'] = $ticket['price'];
                $new_order['ticket_id'] = $ticket_id;
            }

            if (!$mdl_order->save($new_order)) {
                $this->end(false, '预约失败!');
            }
            $order_success_url = $this->gen_url(array(
                'app' => 'experiencestore',
                'ctl' => 'mobile_activity',
                'act' => 'order_success',
                'args' => array($new_order['id']),
            ));

            $this->end(true, '报名预约成功', $order_success_url);
        }
    }
    public function order_success($id)
    {
        $member_id = vmc::singleton('b2c_user_object')->get_member_id();
        if (!$member_id) {
            $this->splash('error', $this->gen_url(array(
                'app' => 'b2c',
                'ctl' => 'mobile_passport',
                'act' => 'login',
            )), '未登录');
        }
        $mdl_order = $this->app->model('activity_order');
        $mdl_schedule = $this->app->model('activity_schedule');
        $mdl_subject = $this->app->model('activity_subject');
        $mdl_store = $this->app->model('store');
        $order = $mdl_order->getRow('*', array('id' => $id, 'member_id' => $member_id));
        $this->pagedata['order'] = $order;
        $schedule = $mdl_schedule->dump($order['schedule_id']);
        $schedule['from_time_fmt'] = date('Y年m月d日 H:i', $schedule['from_time']);
        $schedule['to_time_fmt'] = date('Y年m月d日 H:i', $schedule['to_time']);
        $this->pagedata['schedule'] = $schedule;
        $subject = $mdl_subject->dump($schedule['subject_id']);
        $this->pagedata['subject'] = $subject;
        $store = $mdl_store->dump($schedule['store_id']);
        $this->pagedata['store'] = $store;
        if ($order['need_ticket'] && $order['payed'] < $order['ticket_price']) {
            $mdl_payapps = app::get('ectools')->model('payment_applications');
            $filter = array(
                'status' => 'true',
                'platform_allow' => array(
                    'pc',
                ),
            );
            if (base_component_request::is_wxapp()) {
                $filter['platform_allow'] = array(
                    'wxapp', //微信小程序
                );
            } elseif (base_mobiledetect::is_hybirdapp()) {
                $filter['platform_allow'] = array(
                    'app', //hybirdapp
                );
            } elseif (base_mobiledetect::is_mobile()) {
                $filter['platform_allow'] = array(
                    'mobile', //H5
                );
            }
            $payapps = $mdl_payapps->getList('*', $filter);
            foreach ($payapps as $k => $value) {
                //vmc::dump($value['app_id']);
                if (in_array($value['app_id'], array('offline', 'cod'))) {
                    unset($payapps[$k]);
                }
            }

            $this->pagedata['payapps'] = $payapps;
            $omath = vmc::singleton('ectools_math');
            $need_pay_money = $omath->number_minus(array(
                $order['ticket_price'],
                $order['payed'],
            ));
            $this->pagedata['need_pay_money'] = $need_pay_money;
        }
        $this->page('mobile/activity/order_success.html');
    }
    public function change_payment($order_id)
    {
        $params = utils::_filter_input($_POST);
        $mdl_order = $this->app->model('activity_order');
        $order = $mdl_order->dump($order_id);
        if (!$order) {
            $this->splash('error');
        } else {
            $order['pay_app_id'] = $params['pay_app_id'];
            if ($mdl_order->save($order)) {
                $this->splash('success', null, '成功');
            }
        }
    }
    public function order_payment($order_id, $recursive = false)
    {
        $member_id = vmc::singleton('b2c_user_object')->get_member_id();
        if (!$member_id) {
            $this->splash('error', $this->gen_url(array(
                'app' => 'b2c',
                'ctl' => 'mobile_passport',
                'act' => 'login',
            )), '未登录');
        }

        $mdl_order = $this->app->model('activity_order');
        $order = $mdl_order->dump($order_id);
        if (!$order || $order['member_id'] != $member_id) {
            $this->splash('error', null, '非法操作');
        }
        $mdl_bills = app::get('ectools')->model('bills');

        $bill_sdf = array(
            'order_id' => $order_id,
            'bill_type' => 'payment',
            'pay_mode' => 'online',
            'app_id' => 'experiencestore',
            'pay_object' => 'order',
            'money' => $order['ticket_price'] - $order['payed'],
            'member_id' => $order['member_id'],
            'status' => 'ready',
            'pay_app_id' => $order['pay_app_id'],
            'pay_fee' => 0,
        );
        $obj_bill = vmc::singleton('ectools_bill');

        $exist_bill = $mdl_bills->getRow('*', $bill_sdf);
        //一天内重复利用原支付单据
        if ($exist_bill && !empty($exist_bill['bill_id']) && $exist_bill['createtime'] + 86400 > time()) {
            $bill_sdf = array_merge($exist_bill, $bill_sdf);
        } else {
            $bill_sdf['bill_id'] = $mdl_bills->apply_id($bill_sdf);
        }
        $bill_sdf['return_url'] = $this->gen_url(array(
            'app' => 'experiencestore',
            'ctl' => 'mobile_activity',
            'act' => 'order_payment_result',
            'args' => array(
                $bill_sdf['bill_id'],
            ),
        ));

        //微信内支付时，需要静默授权，以获得用户openid
        if (base_mobiledetect::is_wechat() && $order['pay_app_id'] == 'wxpay' && empty($bill_sdf['payer_account'])) {
            $wxpay_setting = unserialize(app::get('ectools')->getConf('wechat_payment_applications_wxpay'));
            $wxpay_appid = $wxpay_setting['appid'];
            $wxpay_appsecret = $wxpay_setting['appsecret'];
            $auth_code = $_GET['code'];
            $auth_state = $_GET['state'];
            if (!$recursive) {
                $oauth_redirect = $this->gen_url(array(
                    'app' => 'experiencestore',
                    'ctl' => 'mobile_activity',
                    'act' => 'order_payment',
                    'args' => array('recursive'),
                    'full' => 1,
                ));
                $oauth_redirect = urlencode($oauth_redirect);
                //$auth_state = $[]
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
                $this->splash('error', null, $msg);
            }
        } catch (Exception $e) {
            $this->splash('error', null, $e->getMessage());
        }
        $get_way_params = $bill_sdf;
        if (!vmc::singleton('ectools_payment_api')->redirect_getway($get_way_params, $msg)) {
            $this->splash('error', null, $msg);
        }
    }

    public function order_payment_result($bill_id)
    {
        $member_id = vmc::singleton('b2c_user_object')->get_member_id();
        if (!$member_id) {
            $this->splash('error', $this->gen_url(array(
                'app' => 'b2c',
                'ctl' => 'mobile_passport',
                'act' => 'login',
            )), '未登录');
        }
        $mdl_bills = app::get('ectools')->model('bills');
        $bill = $mdl_bills->dump($bill_id);
        if ($bill['member_id'] != $member_id) {
            $this->splash('error', null, '非法操作');
        }
        $order_success_url = $this->gen_url(array(
            'app' => 'experiencestore',
            'ctl' => 'mobile_activity',
            'act' => 'order_success',
            'args' => array($bill['order_id']),
        ));
        if ($bill['status'] == 'succ' || $bill['status'] == 'progress') {
            $this->splash('success', $order_success_url, '支付成功');
        } else {
            $this->splash('error', $order_success_url, '支付失败');
        }
    }

    /**
     *  filter html for applet.
     */
    private function html_filter($html)
    {
        $html_filter_conf = new HTMLFilterConfiguration();
        $allow_tag = array('p','br','ul','li','ol','table','tbody','tr','td','th','tfoot','thead','img');
        foreach ($allow_tag as $tag_name) {
            $html_filter_conf->allowTag($tag_name);
        }
        $html_filter_conf->allowAttribute('img', 'src');
        $html_filter = new HTMLFilter();
        $return = $html_filter->filter($html_filter_conf, $html);
        $return = preg_replace("/&#([\d]+);/","", $return);
        return $return;
    }
}
