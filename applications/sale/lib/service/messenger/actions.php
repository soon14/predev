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

class sale_service_messenger_actions
{
    public function get_actions()
    {
        $actions = array(
            'alert_sale' => array(
                'label' => ('预约活动提醒') ,
                'level' => 11,
                'lock' => false,
                'env_list'=>array(
                    'name'=>'活动名称',
                    'start'=>'开始时间',
                    'end'=>'结束时间',
                ),
            ) ,
        );
        return $actions;
    }
}
