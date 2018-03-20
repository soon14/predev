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


$setting = array(
'site_decimal_digit_count' => array('type' => SET_T_ENUM,'default' => 2,'desc' => '金额运算精度保留位数','options' => array(0 => '整数取整',1 => '取整到1位小数',2 => '取整到2位小数',3 => '取整到3位小数')),//WZP
'site_decimal_type_count' => array('type' => SET_T_ENUM,'default' => 1,'desc' => '金额运算精度取整方式','options' => array('1' => '四舍五入','2' => '向上取整','3' => '向下取整')),//WZP
'site_decimal_digit_display' => array('type' => SET_T_ENUM,'default' => 2,'desc' => '金额显示保留位数','options' => array(0 => '整数取整',1 => '取整到1位小数',2 => '取整到2位小数',3 => '取整到3位小数')),//WZP
'site_decimal_type_display' => array('type' => SET_T_ENUM,'default' => 1,'desc' => '金额显示取整方式','options' => array('1' => '四舍五入','2' => '向上取整','3' => '向下取整')),
'system_area_depth' => array('type' => SET_T_INT,'default' => '3','desc' => '地区级数'),
);
