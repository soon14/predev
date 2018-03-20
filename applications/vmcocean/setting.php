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
    'data_warehouse_url' => array(
        'type' => 'url',
        'default' => '',
        'desc' => 'VMCOcean数据仓库地址<i class="font-red">*</i>',
    ) ,
    'api_screct'=>array(
        'type'=>'text',
        'default'=>'',
        'desc'=>'API Secret',
        'helpinfo'=>'用于查询数据仓库数据用'
    ),
    'enabled' => array(
        'type' => 'select',
        'options' => array(
            'true' => '是',
            'false' => '否',
        ),
        'default' => 'false',
        'desc' => '是否开启',
    ),
    'debug_model' => array(
        'type' => 'select',
        'options' => array(
            'enabled' => '仅开启调试',
            'enabledandpost' => '开启调试并同时发送数据',
            'disabled' => '否',
        ),
        'default' => 'disabled',
        'desc' => '是否开启调试模式',
        'helpinfo'=>'调试模式将在logs/ALERT中打印数据结果'
    ),

);
