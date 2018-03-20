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

class couponactivity_messenger_activity
{
    public function get_actions()
    {
        $actions = array(
            'coupon-achieve' => array(
               'label' => '优惠券领取成功时' ,
               'level' => 9,
               'env_list'=>array(
                   'name' =>'优惠券名称',
                   'description' =>'优惠券规则描述',
                   'from_time' =>'优惠券起始时间',
                   'to_time' =>'优惠券截止时间',
                   'cpns_no' => '优惠券使用码',
               ),
               'exclude'=>array(
                   //'b2c_messenger_msgbox',
                   'b2c_messenger_email'
               )
            ) ,
        );
        return $actions;
    }
}
