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

class experiencestore_messenger_activity
{
    public function get_actions()
    {
        $actions = array(
            'alert_activity' => array(
                'label' => ('门店预约活动提醒') ,
                'level' => 11,
                'lock' => false,
                'env_list'=>array(
                    'activity_order' =>'预约单号',
                    'store_name' =>'门店名称',
                    'store_address' =>'门店地址',
                    'subject_title'=>'主题名称',
                    'code'=>'场次编码',
                    'from_time'=>'开始时间',
                    'to_time'=>'结束时间',
                ),
            ) ,
        );
        return $actions;
    }
}
