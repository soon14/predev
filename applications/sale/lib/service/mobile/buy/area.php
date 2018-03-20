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



class sale_service_mobile_buy_area{
    public function get_buy_area($product){
        $mdl_sales = app::get('sale')->model('sales');
        $sale = $mdl_sales->getRow('*',array('goods_id'=>$product['goods_id'],'status'=>'0'));
        if(!empty($sale)){
            $render = new base_render(app::get('sale'));
            $render->pagedata['data_detail'] = $product;
            $render->pagedata['sale'] = $sale;
            return $render->fetch('mobile/product/buy_area.html'); 
        }else{
            return false;
        }
    }
}
