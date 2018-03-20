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


class groupbooking_member_m_menuextends
{
    /**
     * 构造方法.
     *
     * @param object app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 生成自己app会员中心的菜单.
     *
     * @param array - 会员中心的菜单数组，引用值
     * @param array - url 参数
     *
     * @return bool - 是否成功
     */
    public function get_extends_menu(&$menus)
    {

        $aftersales_menu = array(
            'label' => ('拼团') ,
            'ordernum' => 20,
            'items' => array(
                array(
                    'label' => ('我的拼团') ,
                    'ordernum' => 0,
                    'link' => array(
                        'app' => 'groupbooking',
                        'ctl' => 'mobile_member',
                        'act' => 'orders_list',
                    ),
                ) ,
            ),
        );

        array_push($menus,$aftersales_menu);
        return true;
    }
}
