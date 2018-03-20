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

class commission_ctl_site_member extends b2c_ctl_site_member
{

    public function __construct(&$app)
    {
        $this->app_current = $app;
        $this->app_b2c = app::get('b2c');
        parent::__construct($this->app_b2c);
    }

    /*
     * 成为分佣者
     */
    public function become()
    {
        $member = $this->app_current->model('member_relation')->getRow('*',
            array('member_id' => $this->member['member_id']));
        if ($member['is_commission'] == '1') {
            $this->redirect(array('app' => 'b2c', 'ctl' => 'site_member', 'act' => 'index'));
        }
        $need_sub_domain = $this ->app_current ->getConf('sub_domain');
        if ($this->_request->is_post()) {
            $data = utils::_filter_input($_POST);
            $data['member_id'] = $this->member['member_id'];
            $this->begin();
            try {
                if($need_sub_domain){    //采用二级域名
                    $this->app_current->model('member_relation')->check_domain(trim($data['domain_pre']));
                }
                vmc::singleton('commission_service_member')->become_commission($data);
            } catch (Exception $e) {
                $this->end(false, $e->getMessage());
            }
            $this->end('true', "操作成功");
        } else {
            $redirect = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $this->gen_url(array(
                'app' => 'b2c',
                'ctl' => 'site_member',
                'act' => 'index'
            ));
            $pam_data = vmc::singleton('b2c_user_object')->get_pam_data('*', $this->member['member_id']);
            $this->pagedata['member_mobile'] = $pam_data['mobile']['login_account'] ? $pam_data['mobile']['login_account'] : false;
            $this->pagedata['root_domain'] = $this->app_current->getConf('root_domain');
            $this->pagedata['redirect'] = $redirect;
            $this ->pagedata['need_sub_domain'] = $need_sub_domain;
            $this->output('commission');
        }
    }


    /*
     * 检查域名是否可用
     */
    public function check_domain()
    {
        try {
            $this->app_current->model('member_relation')->check_domain(trim($_GET['domain']));
        } catch (Exception $e) {
            $this->splash('error', '', $e->getMessage());
        }
        $this->splash('success', '', '可以使用');
    }

    /*
     * 我的分佣
     */
    public function my()
    {
        $this->_check_is_commission();
        //上关系
        $member = $this->app_current->model('member_relation')->getRow("parent_id",
            array("member_id" => $this->member['member_id']));
        $user_obj = vmc::singleton('b2c_user_object');
        if ($member['parent_id'] > 0) {
            $this->pagedata['parent'] = $user_obj->get_member_info($member['parent_id']);
        }
        //下级信息获取
        $children = $this->app_current->model('member_relation')->getList("member_id",
            array("parent_id" => $this->member['member_id']));

        //我的分佣
        $mdl_orderlog_achieve = $this->app_current->model('orderlog_achieve')->getList("orderlog_id ,achieve_fund",
            array("member_id" => $this->member['member_id']));
        $orderlog_achieve = utils::array_change_key($mdl_orderlog_achieve, 'orderlog_id');
        $orderlogid = array_keys($orderlog_achieve);

        //根据时间筛选
        $from = $_GET['dashboard_from'] ? strtotime($_GET['dashboard_from']) : 0;
        $to = $_GET['dashboard_to'] ? strtotime($_GET['dashboard_to']) : time();
        $this->pagedata['current_time_w'] = date('Y-m-d H:i:s' ,strtotime("-7 days"));
        $this->pagedata['current_time_m'] = date('Y-m-d H:i:s' ,strtotime("-1 month"));
        $this->pagedata['current_time_q'] = date('Y-m-d H:i:s' ,strtotime("-3 months"));
        $this->pagedata['dashboard_from'] = $_GET['dashboard_from'] ? $_GET['dashboard_from']:date('Y-m-d H:i:s' ,strtotime("-7 days"));
        $this->pagedata['dashboard_to'] = $_GET['dashboard_to'] ? $_GET['dashboard_to']:date('Y-m-d H:i:s');

        //分佣记录统计分页
        $page = $this->get_limit(5);
        $total = $this->app_current->model('orderlog')->count(array("orderlog_id" => $orderlogid, "createtime|between" => array($from, $to)));
        $params = utils::_filter_input($_GET);
        $this->pagedata['query'] = $this->_query_str($params, 0);
        $this->pagedata['pager'] = $this->get_pager($total, $page, $params, 'index');

        $orderlog_list = $this->app_current->model('orderlog')->getList("*",
            array("orderlog_id" => $orderlogid, "createtime|between" => array($from, $to)),
            $page['size'] * ($page['index'] - 1),
            $page['size'], 'createtime desc'
        );
        foreach ($orderlog_list as $k => $v) {
            $orderlog_list[$k]['achieve_fund'] = $orderlog_achieve[$v['orderlog_id']]['achieve_fund'];
            if (!empty($children) && $v['settle_status'] == 1 && in_array($v['from_id'],
                    array_keys(utils::array_change_key($children, 'member_id')))
            ) {
                $achieve[$v['from_id']] += $orderlog_achieve[$v['orderlog_id']]['achieve_fund'];
            }
        }
        if (!empty($children)) {
            foreach ($children as $k => $v) {
                $member_info = $user_obj->get_member_info($v['member_id']);
                $member_info['bring_commission'] = $achieve[$v['member_id']] ? $achieve[$v['member_id']] : 0;
                $this->pagedata['children'][] = $member_info;
            }
        }

        $this->pagedata['orderlog'] = $orderlog_list;
        $this->pagedata['commission_rule'] = $this->app_current->getConf('commission_rule');
        $this->output('commission');
    }

    /*
     * 提取佣金
     */
    public function cash()
    {
        $this->_check_is_commission();
        if ($this->_request->is_post()) {
            $data = utils::_filter_input($_POST);
            $data['money'] = $data['money'] / $this->app_current->getConf('exchange');
            $this->begin();
            try {
                $this->app_current->model('cash')->get_cash($data, $this->member['member_id']);
            } catch (Exception $e) {
                $this->end(false, $e->getMessage());
            }
            $this->end(true, '提现成功');
        } else {
            $member_info = $this->app_current->model('member_relation')->getRow('*',
                array('member_id' => $this->member['member_id']));
            if($member_info['bank_type']){
                $member_info['bank_type'] = $this->app_current->model('bank')->get_bank_name($member_info['bank_type']);
                $member_info['bank_account'] = substr($member_info['bank_account'], -4);
            }
            $this->pagedata['member_info'] = $member_info;
            $this->pagedata['cash_rule'] = array(
                'min_cash' => $this->app_current->getConf('min_cash'),
                'exchange' => $this->app_current->getConf('exchange'),
                'last_cash_time' => strtotime(date('Y-m') . '-' . $this->app_current->getConf('last_cash_time')),
            );
            $this->output('commission');
        }
    }


    /*
     * 我的账户
     */
    public function account()
    {
        $this->_check_is_commission();
        $exchange = $this->app_current->getConf('exchange');
        $member = $this->app_current->model('member_relation')->getRow("*",
            array("member_id" => $this->member['member_id']));
        $all_fund = ($member['used_fund'] + $member['frozen_fund']) * $exchange;
        $member_info = array(
            'name' => $this->member['uname'],
            'used_fund' => $member['used_fund'] * $exchange,
            'frozen_fund' => $member['frozen_fund'] * $exchange,
            'all_fund' => $all_fund,
        );

        //账单记录分页,只取佣金收入，提现，和提现失败的数据
        $page = $this->get_limit(5);
        $total = $this->app_current->model('fundlog')->count(array(
            "member_id" => $this->member['member_id'],
            "type" => array(2, 4, 6)
        ));
        $params = utils::_filter_input($_GET);
        $this->pagedata['query'] = $this->_query_str($params, 0);
        $this->pagedata['pager'] = $this->get_pager($total, $page, $params, 'account');
        $fundlog = $this->app_current->model('fundlog')->getList("*",
            array("member_id" => $this->member['member_id'], "type" => array(2, 4, 6)),
            $page['size'] * ($page['index'] - 1),
            $page['size'], 'opt_time desc'
        );

        $this->pagedata['fundlog'] = $fundlog;
        $this->pagedata['member_list'] = $member_info;
        $this->pagedata['cash_rule'] = $this->app_current->getConf('cash_rule');
        $this->output('commission');
    }


    /*
     * 绑定银行卡
     */
    public function bank()
    {
        $this->_check_is_commission();
        if ($this->_request->is_post()) {
            $data = utils::_filter_input($_POST);
            $pam_data = vmc::singleton('b2c_user_object')->get_pam_data('*', $this->member['member_id']);
            $mobile = $pam_data['mobile']['login_account'];
            //验证码校验
            if (!vmc::singleton('b2c_user_vcode')->verify($data['vcode'], $mobile, 'reset')) {
                $this->splash('failed', '', '验证码错误!');
            }
            $member_data = array(
                'member_id' => $this->member['member_id'],
                'bank_type' => $data['bank_type'],
                'bank_account' => $data['bank_account'],
                'account_name' => $data['account_name'],
            );
            if (false == $this->app_current->model('member_relation')->save($member_data)) {
                $this->splash('failed', '', '操作失败!');
            }
            $this->splash('success', '', '操作成功!');
        } else {
            $member_info = $this->app_current->model('member_relation')->getRow('*',
                array('member_id' => $this->member['member_id']));
            $member_info['bank_account'] = substr($member_info['bank_account'], -4);
            $member_info['bank_type'] = $this->app_current->model('bank')->get_bank_list($member_info['bank_type']);
            $this->pagedata['member_info'] = $member_info;
            $this->pagedata['bank'] = $this->app_current->model('bank')->get_bank_list();
            $this->output('commission');
        }
    }

    /*
     * 检查是否为分佣者
     */
    private function _check_is_commission()
    {
        $member = $this->app_current->model('member_relation')->getRow('*',
            array('member_id' => $this->member['member_id']));
        if (!$member || $member['is_commission'] === '0') {
            $this->redirect(array('app' => 'commission', 'ctl' => 'site_member', 'act' => 'become'));
        }
    }

    //短信发送验证码
    public function send_vcode_sms($type = 'reset')
    {
        $passport_obj = vmc::singleton('b2c_user_passport');
        $pam_data = vmc::singleton('b2c_user_object')->get_pam_data('*', $this->member['member_id']);
        if ($pam_data['mobile']) {
            $mobile = $pam_data['mobile']['login_account'];
        } else {
            $mobile = trim($_POST['mobile']);
            if (empty($mobile)) {
                $this->splash('error', null, '请输入手机号码');
            }
            if (!$passport_obj->check_signup_account($mobile, $msg)) {
                $this->splash('error', null, $msg);
            }
            if ($msg != 'mobile') {
                $this->splash('error', null, '错误的手机格式');
            }
        }

        $uvcode_obj = vmc::singleton('b2c_user_vcode');
        $vcode = $uvcode_obj->set_vcode($mobile, $type, $msg);
        if ($vcode) {
            //发送验证码 发送短信
            $data['vcode'] = $vcode;
            if (!$uvcode_obj->send_sms($type, (string)$mobile, $data)) {
                $this->splash('error', null, '短信发送失败');
            }
        } else {
            $this->splash('failed', null, $msg);
        }
        $this->splash('success', null, '短信已发送');
    }

    /**
     * 获取分页参数
     */
    private function get_limit($size = 10)
    {
        $params = utils::_filter_input($_GET);
        $page['index'] = $params['page'] ? $params['page'] : 1;
        $page['size'] = $params['page_size'] ? $params['page_size'] : $size;

        return $page;
    }

    /**
     * 获取分页
     */
    private function get_pager($total, $page, $params, $action)
    {
        $query_str = $this->_query_str($params);
        $page_info = array(
            'total' => ($total ? ceil($total / $page['size']) : 1),
            'current' => intval($page['index']),
        );
        $page_info['token'] = time();
        $page_info['link'] = $this->gen_url(array(
                'app' => 'commission',
                'ctl' => 'site_member',
                'act' => $action
            )) . '?page=' . $page_info['token'] . ($query_str ? '&' . $query_str : '');

        return $page_info;
    }

    private function _query_str($params, $nopage = true)
    {
        if ($nopage) {
            unset($params['page']);
        }

        return http_build_query($params);
    }

    //我的分享二维码
    public function myqrcode(){
        $this->_check_is_commission();
        $mobile_url=($_SERVER['REQUEST_SCHEME']?$_SERVER['REQUEST_SCHEME']:'http').'://'.$_SERVER['HTTP_HOST'];
        if(!$this ->app_current ->getConf('sub_domain')){    //不采用二级域名
            $mobile_url=$mobile_url.'#fmid='.$this->member['member_id'];
        }
        $this->pagedata['url'] = $mobile_url;
        $this->output('commission');
    }
}
