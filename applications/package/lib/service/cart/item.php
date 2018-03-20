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
class package_service_cart_item extends b2c_frontpage
{
    public function get_item($cart_object)
    {
        $app = app::get('package');
        $render = new base_render($app);
        $render->pagedata['cart_result'] = $cart_object;
        $render->pagedata['self'] = $app->getConf('self');
        return $render->fetch('site/cart/item.html'); 
    }
}
