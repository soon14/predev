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



class codebuy_service_buy_area{
    public function get_buy_area($product){
        $mdl_activity = app::get('codebuy')->model('activity');
        $now = time();
        $activity = $mdl_activity->getRow('*',array(
                    'goods_id'=>$product['goods_id'],
                    'status'=>'0',
                    'start|sthan'=>$now,
                    'end|bthan'=>$now,
                    ));
        if(!empty($activity)){
            $render = new base_render(app::get('codebuy'));
            $render->pagedata['is_login'] = $is_login = vmc::singleton('b2c_user_object')->is_login();
            $render->pagedata['data_detail'] = $product;
            $render->pagedata['activity'] = $activity;
            return $render->fetch('site/product/buy_area.html'); 
        }else{
            return false;
        }
    }
}
