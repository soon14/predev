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
class registercoupon_service_member_create
{
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function create_after($member_id){
        $now = time();
        $mdl_rule = $this ->app ->model('rule');
        $rules = $mdl_rule ->getList('*' ,array('rule_status' =>'1' ,'from_time|lthan' => $now ,'to_time|than' =>$now));
        $mdl_coupon = app::get('b2c')->model('coupons');
        if(is_array($rules)){
            foreach($rules as $v){
                if($v['cpns_id']){
                    $cpns_id = $v['cpns_id'];
                    $coupon = $mdl_coupon->dump($cpns_id);
                    if($coupon['cpns_status'] == '0') {
                        continue;
                    }
                    $list = $mdl_coupon->downloadCoupon($cpns_id, 1, '1', '注册赠送', 'system');
                    $code = $list[0];
                    vmc::singleton('b2c_coupon_stage') -> isAvailable($member_id ,$code ,$msg);
                }
            }
        }
    }
}