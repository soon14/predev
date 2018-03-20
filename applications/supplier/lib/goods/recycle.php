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

class supplier_goods_recycle {

    //删除商品前操作
    public function exec($rows,&$msg) {

        $gids = array_keys(utils::array_change_key($rows, 'goods_id'));
        $mdl_relgoods = app::get('supplier')->model('relgoods');
        if($mdl_relgoods->count(array('goods_id'=>$gids))) {
            $msg = '商品：'.implode(',',array_keys(utils::array_change_key($rows,'name'))).'该商品包含供应商商品，请先解绑';
            return false;
        };
        return true;
    }

}