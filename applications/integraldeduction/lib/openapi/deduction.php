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

class integraldeduction_openapi_deduction extends base_openapi
{
    public function cart($params)
    {
        $in_use = $params['enabled'];
        $integral = $params['integral'];
        $current_member = vmc::singleton('b2c_cart_stage')->get_member();
        if (!$current_member) {
            $this->failure('未知会员');
        }
        $member_integral_position = $current_member['integral'];
        if ($integral > $member_integral_position || !$integral) {
            $integral = $member_integral_position;
        }
        if (isset($in_use)) {
            $_SESSION['INTEGRAL_DEDUCTION_USE'] = $integral;
        } else {
            unset($_SESSION['INTEGRAL_DEDUCTION_USE']);
        }
        $this->success(array('member_integral_position' => $member_integral_position));
    }

    public function get_position(){
        $current_member = vmc::singleton('b2c_cart_stage')->get_member();
        if (!$current_member) {
            $this->failure('未知会员');
        }
        $member_integral_position = $current_member['integral'];
        $this->success(array('member_integral_position' => $member_integral_position));
    }
}
