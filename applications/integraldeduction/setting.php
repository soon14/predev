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
    'enabled' => array(
        'type' => 'select',
        'options' => array(
            'false' => '不启用',
            'true' => '启用',
        ),
        'default' => 'false',
        'desc' => '是否启用积分抵扣',
    ) ,
    'currency_rate' => array(
        'type' => 'text',
        'default' => 1,
        'desc' => '与货币汇率',
        'helpinfo'=>'如：1积分等于1元,则填写1，1积分等于0.1元，则填写0.1'
    ) ,
    'order_scale_enabled'=>array(
        'type' => 'select',
        'options' => array(
            'false' => '不启用',
            'true' => '启用',
        ),
        'default' => 'false',
        'desc' => '是否启用订单积分抵扣',
        'helpinfo'=>'<span class="label label-primary">订单积分抵扣和商品级积分抵扣只能同时运行一种</span>'
    ),
    'order_scale' => array(
        'type' => 'text',
        'default' => '0.1',
        'desc' => '最多可抵扣订单金额比例',
        'helpinfo'=>'积分最多可抵扣订单金额比例。请填写小数。如 10%比例，填写0.1<br><span class="label label-warning">该配置项，仅在启用订单积分抵扣状态下可用</span>'
    ) ,
);
