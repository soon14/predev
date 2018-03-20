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


class commission_member_m_menuextends
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    /*
     * 会员中心。分佣菜单
     */
    public function get_extends_menu(&$menus)
    {

        $seller_menu = array(
            'label' => ('分佣'),
            'ordernum' => 70,
            'items' => array(
                array(
                    'label' => ('我的分佣'),
                    'ordernum' => 0,
                    'link' => array(
                        'app' => 'commission',
                        'ctl' => 'mobile_member',
                        'act' => 'my',
                    ),
                ),
                array(
                    'label' => ('申请提现'),
                    'ordernum' => 1,
                    'link' => array(
                        'app' => 'commission',
                        'ctl' => 'mobile_member',
                        'act' => 'cash',
                    ),
                ),
                array(
                    'label' => ('我的账户'),
                    'ordernum' => 2,
                    'link' => array(
                        'app' => 'commission',
                        'ctl' => 'mobile_member',
                        'act' => 'account',
                    ),
                ),
                array(
                    'label' => ('我的推广'),
                    'ordernum' => 3,
                    'link' => array(
                        'app' => 'commission',
                        'ctl' => 'mobile_member',
                        'act' => 'myqrcode',
                    ),
                ),
            ),
        );
        array_push($menus, $seller_menu);

    }
}
