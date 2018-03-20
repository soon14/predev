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


class groupbooking_order_beforefinish
{
    /**
     * 公开构造方法.
     *
     * @params app object
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function exec(&$order_sdf, $activity, &$msg)
    {
        if($order_sdf['main_id']) {
            $count = $this->app->model('orders')->count(array('main_id'=>$order_sdf['main_id'],'activity_id'=>$order_sdf['activity_id'],'status|noequal'=>'2'));
            $count += 1;//加上主单
            if($count >= $activity['people_number']) {
                $msg = '该团参与人数已达上线';
                return false;
            }
        }
        return true;
    }

}