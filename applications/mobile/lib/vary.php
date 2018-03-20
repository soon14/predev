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
 * 缓存数据键值.
 */
class mobile_vary
{
    public function get_varys()
    {
        return array(
                        'IS_MOBILE' => base_mobiledetect::is_mobile(),
                        'IS_WECHAT' => base_mobiledetect::is_wechat(),
                        'IS_HYBIRDAPP' => base_mobiledetect::is_hybirdapp(),
                        'IS_WXAPP' => base_component_request::is_wxapp(),
                    );
    }
}
