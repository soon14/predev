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


class ectools_view_compiler
{
    public function compile_modifier_cur($attrs, &$compile)
    {
        return $this->compile_modifier_cur_odr($attrs, $compile);
    }
    public function compile_modifier_cur_odr($attrs, &$compile)
    {
        return 'vmc::singleton(\'ectools_math\')->formatNumber('.$attrs.',app::get(\'ectools\')->getConf(\'site_decimal_digit_count\'),app::get(\'ectools\')->getConf(\'site_decimal_digit_count\'))';
    }
}
