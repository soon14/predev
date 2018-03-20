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
class commission_mdl_cash extends dbeav_model{
    public function __construct($app){
        $this ->app = $app;
        parent::__construct($app);
    }
    /*
    * 申请提现
     */
    public function get_cash($data, $member_id)
    {

        //验证码校验
        $pam_data = vmc::singleton('b2c_user_object')->get_pam_data('*', $member_id);
        if(!$pam_data['mobile']){
            throw new Exception('请先绑定手机号码');
        }

        if (!vmc::singleton('b2c_user_vcode')->verify($data['vcode'], $pam_data['mobile']['login_account'], 'reset')) {
            throw new Exception('验证码错误');
        }
        $cash_time = app::get('commission')->getConf('last_cash_time');
        $cash_time = strtotime(date('Y-m').'-'.$cash_time);
        if ($cash_time < time()) {
            throw new Exception('不在提现时间范围');
        }

        $min_cash = app::get('commission')->getConf('min_cash');
        if ($data['money'] < $min_cash) {
            throw new Exception('提取金额小于最低最低额度');
        }
        $member = $this->app->model('member_relation')->getRow("*", array("member_id" => $member_id));
        if ($data['money'] > $member['used_fund']) {
            throw new Exception('账户可用资金不足');
        }

        if (!$member['bank_account']) {
            throw new Exception('请您先绑定银行卡');
        }
        $cash_data = array(
            'member_id' => $member_id,
            'apply_fund' => $data['money'],
            'bank_type' => $member['bank_type'],
            'bank_account' => $member['bank_account'],
            'account_name' => $member['account_name'],
            'status' => '1',
            'createtime' => time()
        );
        if (!$this->save($cash_data)) {
            throw new Exception('提现申请失败');
        }
        $fund_data = array(
            'member_id' => $member_id,
            'change_fund' => -$data['money'], //实际变动
            'type' => '4',
            'opt_id' => $member_id,
            'opt_type' => 'member',
            'mark' => "申请提现，扣除",
            'frozen_change' => +$data['money'],
            'extfield' => $cash_data['cash_id']
        );
        $member_service = vmc::singleton('commission_service_member');
        if (false == $member_service->fund_change($fund_data, $msg)) {
            throw new Exception($msg);
        }
    }

    /*
     * 提现处理
     */
    public function do_cash($cash_id ,$status){
        $cash = $this ->getRow('*' ,array('cash_id' => $cash_id));
        if(!$cash || $cash['status'] != 1){
            throw new Exception('该提现申请已处理');
        }
        if($status ==2 ){//提现成功
            $change_fund = 0;
            $fund_type = '5';
            $mark = "提现成功扣除";
        }elseif($status ==3){//提现失败
            $change_fund = +$cash['apply_fund'];
            $fund_type = '6';
            $mark = "提现失败返还";
        }else{
            throw new Exception('参数错误');
        }
        if(false == $this ->update(array('status' =>$status) ,array('cash_id' => $cash_id))){
            throw new Exception('提现状态修改失败');
        }
        $user = vmc::singleton('desktop_user');
        $fund_data = array(
            'member_id' => $cash['member_id'],
            'change_fund' => $change_fund, //实际变动
            'type' => $fund_type,
            'opt_id' => $user->uid,
            'opt_type' => 'shopadmin',
            'mark' => $mark,
            'frozen_change' => -$cash['apply_fund'],
            'extfield' => $cash_id
        );
        $member_service = vmc::singleton('commission_service_member');
        if (false == $member_service->fund_change($fund_data, $msg)) {
            throw new Exception($msg);
        }
    }
}