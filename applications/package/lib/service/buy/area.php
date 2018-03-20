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



class package_service_buy_area{
    public function get_buy_area($product){
        $mdl_package = app::get('package')->model('rules');
        $now = time();
        $package = $mdl_package->getList('*',array('goods_id'=>$product['goods_id'],'status'=>'0','start|lthan'=>$now,'end|than'=>$now));
        if(!empty($package)){
            $mdl_goods = app::get('b2c')->model('goods');
            $render = new base_render(app::get('package'));
            foreach($package as $key=>$item){
                $total_price = 0;
                $total_package_price = 0;
                foreach($item['package_goods'] as $package_goods_key=>$package_goods){
                    $tmp_goods = $mdl_goods->dump(array('goods_id'=>$package_goods['goods_id']),'*','default');
                    $total_price += current($tmp_goods['product'])['price'];
                    $total_package_price += $package_goods['package_price'];
                    $package[$key]['package_goods'][$package_goods_key] = array_merge($tmp_goods,$package_goods);
                }
                $package[$key]['total_price'] = $total_price;
                $package[$key]['total_package_price'] = $total_package_price;
            }
            $render->pagedata['package'] = $package;
            $render->pagedata['data_detail'] = $product;
            return $render->fetch('site/product/buy_area.html'); 
        }else{
            return false;
        }
    }
}
