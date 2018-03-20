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


class ubalance_tasks_income_update extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        //年化率开启的时候，计算收益
        if (app::get('ubalance')->getConf('earnings_type')) {
            $mdl_account = app::get('ubalance')->model('account');
            $nums = $mdl_account->count(array('status' => '1', 'ubalance|than' => 0));
            $step = 500;
            for ($i = 0; $i < ceil($nums / $step); $i++) {
                $account_list = $mdl_account->getList('ubalance,income,member_id',
                    array('status' => '1', 'ubalance|than' => 0), $i * $step, $step);
                system_queue::instance()->publish('ubalance_tasks_income_worker', 'ubalance_tasks_income_worker', $account_list);
            }
        }

        return true;
    }

}
