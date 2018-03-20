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

/**
 *  getConf 默认值.
 */
$setting = array(
    'groupbooking_order_autocancel_time'=>array(
        'type' => 'number',
        'default' => 600,
        'desc' => '订单自动关闭时间（单位：秒）',
        'helpinfo'=>'未付款订单,未及时进行支付操作,系统将自动定时关闭'
    ),


);
