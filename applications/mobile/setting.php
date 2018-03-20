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
 *  key 的格式请用   type.key 格式
 *  base 基础
 *  site 站点
 *  page 页面.
 */
$setting = array(
    'mobile_params_separator' => array(
        'default' => '-',
    ) ,
    'enable_mobile_uri_expanded' => array(
        'default' => 'true',
    ) ,
    'mobile_uri_expanded_name' => array(
        'default' => 'html',
    ) ,
    'check_mobile_uri_expanded_name' => array(
        'default' => 'true',
    ) ,
    'select_terminator' => array(
        'type' => 'select',
        'options' => array(
            'false' => '不启用',
            'true' => '启用',
        ),
        'default' => 'false',
        'desc' => '自动识别设备并跳转到HTML5触屏' ,
    ) ,
    'mobile_name' => array(
        'type' => 'text',
        'default' => 'YOUR MSHOP NAME' ,
        'required' => true,
        'desc' => 'HTML5触屏移动应用名称' ,
    ) ,
    'mobile_icon' => array(
        'type' => 'image',
        'required' => true,
        'desc' => 'HTML5触屏移动应用ICON' ,
    ) ,
    'page_default_title' => array(
        'type' => 'text',
        'default' => 'YOUR MSHOP NAME',
        'desc' => '默认TITLE标题',
    ) ,
    'page_default_keywords' => array(
        'type' => 'text',
        'default' => '',
        'desc' => '默认KEYWORDS关键字',
    ) ,
    'page_default_description' => array(
        'type' => 'textarea',
        'default' => '',
        'desc' => '默认DESCRIPTION简介',
    ) ,
);
