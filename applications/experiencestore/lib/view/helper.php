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


class experiencestore_view_helper
{
    public function modifier_regionpart($region_data, $part = 'province')
    {
        $map = array(
            'province' => 0,
            'city' => 1,
            'area' => 2,
        );
        list($pkg, $regions, $region_id) = explode(':', $region_data);
        if (is_numeric($region_id)) {
            $d = explode('/', $regions);

            return $d[$map[$part]];
        } else {
            return $region_data;
        }
    }
}
