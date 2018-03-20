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


class ubalance_tasks_batchrecharge extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        if(!$params['member_ids']) {
           return true;
        }
        $obj_bill = vmc::singleton('ectools_bill');
        $mdl_bills = app::get('ectools')->model('bills');
        $error_member_ids = array();
        foreach($params['member_ids'] as $member_id) {
            $bill_sdf = array(
                'subject' => $params['ubalance_name'].'充值',
                'bill_type' => 'payment',
                'pay_mode' => 'online',
                'app_id' => 'ubalance',
                'pay_object' => 'recharge',
                'money' => (float) $params['money'],
                'member_id' => $member_id,
                'status' => 'succ',
                'pay_app_id' => 'offline',
                'op_id' => $params['op_id'],
                'pay_fee' => null,
                'memo' => $params['memo'],
            );
            if(!$bill_sdf['bill_id']) {
                $bill_sdf['bill_id'] = $mdl_bills->apply_id($bill_sdf);
            }
            if (!$obj_bill->generate($bill_sdf, $msg)) {
                $error_member_ids[] = $member_id;
            }
        }
        if($error_member_ids) {
            $error_msg = "充值失败会员ID：".implode(',',$error_member_ids);
            logger::error($error_msg);
        }
        return true;
    }

}
