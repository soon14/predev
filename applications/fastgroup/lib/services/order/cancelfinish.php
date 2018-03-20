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


class fastgroup_services_order_cancelfinish
{
    public function exec(&$order_sdf,  &$msg = '')
    {
        $mdl_fgorders = app::get('fastgroup')->model('fgorders');
        $fgorder = $mdl_fgorders->getRow('*', array('order_id' => $order_sdf['order_id']));
        if (!$fgorder) {
            //没有相关快团订单,忽略，返回true;
            return true;
        }
        $fgorder['order_status'] = 'dead';
        $mdl_fgorders->save($fgorder);
        return true;
    }
}
