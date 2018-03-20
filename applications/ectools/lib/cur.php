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


class ectools_cur
{
    static public function format($number)
    {
        return vmc::singleton('ectools_math')->formatNumber(
            $number ,
            app::get('ectools')->getConf('site_decimal_digit_count'),
            app::get('ectools')->getConf('site_decimal_digit_count')
        );
    }
}
