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
 * 会员中心菜单扩展
 */
class sale_service_mmenuextend
{
    public function get_extends_menu(&$mmenu)
    {
        foreach ($mmenu as &$item) {
            if ($item['label'] == '购物') {
                $item['items'][] = array(
                        'label' => ('我的预约') ,
                        'ordernum' => 30,
                        'link' => array(
                            'app' => 'sale',
                            'ctl' => 'site_member',
                            'act' => 'index',
                        ),
                    );
                    break;
            }
        }
    }

    
}
