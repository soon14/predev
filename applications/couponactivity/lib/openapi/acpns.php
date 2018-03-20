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


class couponactivity_openapi_acpns extends base_openapi
{
    public function __construct()
    {
        $this->req_params = vmc::singleton('base_component_request')->get_params(true);
    }
    /**
    * 优惠券促销 活动优惠券领取
    * @param a int 活动ID
    * @param c int 优惠券ID
    * @param k String 优惠券KEY
    */
    public function achieve($args = array())
    {
        $params = array_merge((array)$args,(array)$this->req_params);
        $id = $params['a'];
        $cid = $params['c'];
        $key = $params['k'];
        if($id<1){
            $this->failure('缺少活动信息');
        }
        if($cid<1){
            $this->failure('缺少优惠券信息');
        }
        $user_obj = vmc::singleton('b2c_user_object');
        $member_id = $user_obj->get_member_id();
        if($member_id<1){
            $this->failure('未登录无法领取');
        }
        // 开启事务
        $db = vmc::database();
        $trans_status = $db->beginTransaction();

        $obj_coupon = vmc::singleton('couponactivity_coupon_stage');
        if( !$res_data = $obj_coupon->get_cpns($cid,$id,$member_id,$msg) ){
            // 事务回滚
            $db->rollBack();
            $this->failure($msg);
        }
        // 发送信息
        $pam_data = vmc::singleton('b2c_user_object')->get_pam_data('*',$member_id);
        vmc::singleton('b2c_messenger_stage')->trigger('coupon-achieve',$res_data,array(
            'mobile'=> $pam_data['mobile'],
            'member_id'=> $member_id
        ));

        // 事务提交
        $db->commit($trans_status);
        $this->success($res_data);
    }

}
