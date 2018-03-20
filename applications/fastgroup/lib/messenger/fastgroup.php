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

class fastgroup_messenger_fastgroup
{
    public function get_actions()
    {
        $actions = array(
            'fastgroup-payfinish' => array(
                'label' => '快团支付成功时' ,
                'level' => 9,
                'env_list'=>array(
                    'fg_title' =>'活动名称',
                    'payed' =>'支付金额',
                    'payapp' =>'支付方式名称',
                    'succ_pay_time' =>'支付完成时间',
                    'skey' =>'提货秘钥',
                ),
                'exclude'=>array(
                    'b2c_messenger_msgbox',
                    'b2c_messenger_email'
                )
            ) ,
            'fastgroup-endfinish' => array(
                'label' => '快团订单完成时' ,
                'level' => 9,
                'env_list'=>array(
                    'fg_title' =>'活动名称',
                    'last_modify' =>'完成时间',
                ),
                'exclude'=>array(
                    'b2c_messenger_msgbox',
                    'b2c_messenger_email'
                )
            ) ,
        );
        return $actions;
    }
}
