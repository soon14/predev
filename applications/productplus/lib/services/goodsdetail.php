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


class productplus_services_goodsdetail
{
    public function exec(&$detail){
        $product_id = $detail['product']['product_id'];
        $mdl_extend = app::get('productplus')->model('extend');

        $extend = $mdl_extend->dump(array('product_id'=>$product_id,'enabled'=>'true'),'*','default');
        if(!$extend){
            return true;
        }
        $detail['name'] = $detail['product']['name'] = $extend['title'];
        $detail['brief'] = $extend['brief'];
        if($extend['has_desc'] == 'true' && isset($detail['description'])){
            $detail['description'] = $extend['description'];
        }
        if(!empty($extend['images'])){
            $detail['image_default_id'] = $detail['product']['image_id'] = $extend['image_default'];
            $detail['images'] = $extend['images'];
        }
        return true;

    }

}
