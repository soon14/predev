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


class vshop_service_order_createbefore
{
    public function exec($order_sdf, $cart_result, $msg)
    {
        if ($_POST['_vshop_id']) {
            $mdl_vshop = app::get('vshop')->model('shop');
            $filter =  array('shop_id' => $_POST['_vshop_id'], 'status' => 'active');
            if(app::get('vshop')->getConf('profit_by_self') !='true'){
                //店主自己通过自己的链接购买不分润
                $filter['member_id|notin'] = $order_sdf['member_id'];
            }
            $vshop = $mdl_vshop->getRow('shop_id,name',$filter);
            if (!$vshop) {
                logger::warning('忽略_vshop_id:'.$_POST['_vshop_id']);
                return true;
            }
        }else{
            return true;
        }
        $mdl_relorder = app::get('vshop')->model('relorder');
        $new_relorder = array(
            'order_id'=>$order_sdf['order_id'],
            'shop_id'=>$vshop['shop_id'],
            'shop_name'=>$vshop['name'],
            'createtime'=>time()
        );
        if(!$mdl_relorder->save($new_relorder)){
            logger::error('微店相关订单记录保存失败:');
            logger::error($new_relorder);
        }
        return true;
    }
}
