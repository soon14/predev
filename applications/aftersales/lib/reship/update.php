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


class aftersales_reship_update
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 用于订单退货确认，并影响售后服务单.
     *
     * @params array - 退货单据数据SDF
     *
     * @return bool - 执行成功与否
     */
    public function exec($delivery_sdf, &$msg = '')
    {
        return true;
    }
}
