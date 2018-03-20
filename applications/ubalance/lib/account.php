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
class ubalance_account
{
    public function __construct(&$app)
    {
        $this->app = $app;
    }

    /*
    * 用户资金变动及日志,请在事务中调用
    */
    public function fund_change($data, &$msg)
    {
        $ectools_math = vmc::singleton('ectools_math');
        if (!$data['member_id']) {
            $msg = '参数错误';

            return false;
        }
        $member = $this->app->model('account')->getRow('*', array('member_id' => $data['member_id']));
        if (!$member) {
            $msg = '非法用户';

            return false;
        }
        $log_data = array(
            'member_id' => $member['member_id'],
            'change_fund' => $data['change_fund'],  //实际变动资金
            'frozen_fund' => $data['frozen_change'],
            'type' => $data['type'],
            'current_fund' => $ectools_math->number_plus(array($member['ubalance'], $data['change_fund'])), //当前可用余额
            'opt_id' => $data['opt_id'],
            'opt_type' => $data['opt_type'] ? $data['opt_type'] : 'unknown',
            'opt_time' => $data['opt_time'] ? $data['opt_time'] : time(),
            'mark' => $data['mark'],
            'bill_id' => $data['bill_id'],
            'extfield' => $data['extfield'],

        );
        $member_data = array(
            'member_id' => $member['member_id'],
            'frozen' => $ectools_math->number_plus(array($member['frozen'], $data['frozen_change'])),//冻结资金
            'ubalance' => $ectools_math->number_plus(array($member['ubalance'], $data['change_fund'])),  //可用资金
            'last_modify' => time(),
        );

        switch ($data['type']) {
            /*
             * '1' => '充值',
             *'2' => '支付',
             *'3' => '退款',
             *'4' => '收益',
             *'5' => '冻结',
             *'6' => '提现',
             *'7' => '返还',
             *'8' => '批量充值',
             */
            case '1':
            case '8':
            //累计充值金额
            $member_data['amount'] = $member['amount'] + $data['change_fund'];
            $member_data['frequency'] = $member['frequency'] + 1;
                break;
            case '4':
            //累计收益金额
            $member_data['income'] = $member['income'] + $data['change_fund'];
                break;
        }
        if (!$this->app->model('account')->save($member_data)) {
            $msg = '用户资金变动错误';

            return false;
        }
        if (!$this->app->model('fundlog')->save($log_data)) {
            $msg = '用户资金变动日志错误';

            return false;
        }

        return true;
    }
}
