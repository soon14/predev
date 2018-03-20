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


class integraldeduction_order_beforecreate {

    public function exec(&$order_sdf, $cart_result = array() , &$msg = '') {
        if(!$cart_result['integraldeduction'] || !$cart_result['integraldeduction']['score_u']){
            return true;
        }
        $score_u = $cart_result['integraldeduction']['score_u'];
        $current_member = vmc::singleton('b2c_cart_stage')->get_member();
        if($current_member['integral']<$score_u){
            $msg = '积分余额不足';
            return false;
        }
        if($order_sdf['member_id']!=$current_member['member_id']){
            $msg = '异常操作';
            logger::warning('异常积分抵扣操作.MEMBER_ID:'.$current_member['member_id']);
            return false;
        }
        $integral_charge = array(
            'member_id'=>$order_sdf['member_id'],
            'change_reason'=>'deduction',//抵扣
            'order_id'=>$order_sdf['order_id'],
            'change'=> -($score_u),
            'op_model'=>'member',
            'op_id'=>$current_member['member_id']
        );

        if(!vmc::singleton('b2c_member_integral')->change($integral_charge,$error_msg)){
            $msg = '积分抵扣失败!';
            logger::warning('积分抵扣失败.MEMBER_ID:'.$current_member['member_id']);
            return false;
        }else{
            $order_sdf['score_u'] = $score_u;
            return true;
        }

    }


}
