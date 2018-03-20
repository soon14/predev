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
    'baidutj_code' => array(
        'type' => 'textarea',
        'default' => '',
        'desc' => '百度统计代码',
        'helpinfo'=>'<a href="http://tongji.baidu.com" target="_blank" class="btn btn-link"><i class="fa fa-external-link"></i> 立即登陆百度统计获得统计js代码</a>'
    ) ,
    'ecommerce_tj' => array(
        'type' => 'select',
        'options' => array(
            'true' => '是',
            'false' => '否',
        ),
        'default' => 'true',
        'desc' => '是否开启百度统计电商分析功能？',
        'helpinfo'=>'用于监控您网站页面上的订单数据。能够监控的指标包括:订单数，订单金额、订单转化率、订单投资回报率等。开启此选项后，您还需要登陆到百度统计账户，在应用中心免费开通电商分析功能。电商分析功能统计已完成订单(包括未付款订单)'
    ),

);
