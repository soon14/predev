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


class productplus_services_goodsgallery
{
    public function exec(&$goods_list){
        foreach ($goods_list as &$item) {
            vmc::singleton('productplus_services_goodsdetail')->exec($item);
        }
    }

}
