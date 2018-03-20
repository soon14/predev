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


class store_view_helper
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function modifier_price($value = '', $format = '')
    {
        switch($format){
            case 'int':
                return floor($value);
            case 'float':
                return substr($value ,strpos($value ,'.')+1 ,2);
            default:
                return $value;
        }
    }
}
