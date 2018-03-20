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


class ubalance_tasks_income_worker extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        $account_list = $params;
        $setting = app::get('ubalance')->model('set')->getRow('*');
        if ($account_list && $setting['earnings_type']) {
            $year_ratio = $setting['year_ratio'];
            if (date('L')) {
                $day = 366;
            } else {
                $day = 365;
            }
            foreach ($account_list as $account) {
                $income = round($account['ubalance'] * $year_ratio / $day, 2);
                //如果没有收益额就不进行更新
                if ($income) {
                    $log_data = array(
                        'member_id' => $account['member_id'],
                        'change_fund' => $income,
                        'frozen_fund' => 0,
                        'type' => '4',
                        'opt_id' => 0,
                        'opt_type' => 'system',
                        'opt_time' => time(),
                        'mark' => '年化收益日结'
                    );
                    logger::debug('余额宝年化收益日结'.var_export($log_data,1));
                    if (!vmc::singleton('ubalance_account')->fund_change($log_data, $msg)) {
                        logger::error('余额宝收益日结失败,msg:' . $msg . 'data:' . var_export($log_data, 1));
                    }
                }
            }
        }
        return true;
    }

}
