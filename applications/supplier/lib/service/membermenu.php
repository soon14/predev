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


class supplier_service_membermenu
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    /*
     * pc 会员中心,菜单扩展
     */
    public function get_extends_menu(&$menus)
    {
        $mdl_supplier = $this->app->model('supplier');
        $current_member = vmc::singleton('b2c_user_object')->get_current_member();

        $member_id = $current_member['member_id'];
        if(!$mdl_supplier->count(array('member_id'=>$member_id))){
            return;
        }
        $supplier_menu = array(
            'label' => ('我是供应商'),
            'ordernum' => 90,
            'items' => array(
                array(
                    'label' => ('供应商管理面板'),
                    'ordernum' => 0,
                    'link' => array(
                        'app' => 'supplier',
                        'ctl' => 'site_supplier',
                        'act' => 'index',
                    ),
                ),
            ),
        );
        array_push($menus, $supplier_menu);
    }
}
