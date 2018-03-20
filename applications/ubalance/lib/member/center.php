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
class ubalance_member_center
{

    /**
     * 扩展会员中心余额宝显示
     */
    public function balance_data($member_id)
    {
        $data = app::get('ubalance')->model('set')->getRow('*');
        $balance_account = app::get('ubalance')->model('account')->getRow('*', array('member_id' => $member_id));
        $user_balance = $balance_account['ubalance'];
        $data['user_balance'] = $user_balance ? $user_balance : 0;

        return $data;
    }

}
