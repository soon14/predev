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
    'base_site_params_separator' => array(
        'default' => '-',
    ) ,
    'base_site_page_cache' => array(
        'default' => 'true',
    ) ,
    'base_enable_site_uri_expanded' => array(
        'default' => 'true',
    ) ,
    'base_site_uri_expanded_name' => array(
        'default' => 'html',
    ) ,
    'base_check_uri_expanded_name' => array(
        'default' => 'true',
    ) ,
    'site_name' => array(
        'type' => 'text',
        'default' => 'YOUR SHOP NAME' ,
        'required' => true,
        'desc' => '网站名称' ,
    ) ,
    'page_default_title' => array(
        'type' => 'text',
        'default' => '',
        'desc' => '默认网站标题',
    ) ,
    'page_default_keywords' => array(
        'type' => 'text',
        'default' => '',
        'desc' => '默认网站关键字',
    ) ,
    'page_default_description' => array(
        'type' => 'textarea',
        'default' => '',
        'desc' => '默认网站简介',
    ) ,
);
