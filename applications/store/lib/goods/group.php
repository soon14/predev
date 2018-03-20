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


class store_goods_group{
    function get_extends_group(&$group){
        $group[] = array(
            'label' => ('打印价签') ,
            'data-submit' => 'index.php?app=store&ctl=admin_goods&act=print_price',
            'data-target' => '_ACTION_MODAL_',
        );
    }
}