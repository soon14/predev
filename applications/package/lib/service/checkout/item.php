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
 * PC端 购物车控制器类
 * 主要完成购物车相关操作及操作结果的反馈,页面间引导.
 */
class package_service_checkout_item extends b2c_frontpage
{
    public function get_item($cart_object)
    {
        $render = new base_render(app::get('package'));
        $render->pagedata['cart_result'] = $cart_object;
        return $render->fetch('site/checkout/item.html'); 
    }
}
