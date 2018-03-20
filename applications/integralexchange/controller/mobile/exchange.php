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

class integralexchange_ctl_mobile_exchange extends b2c_ctl_mobile_member
{
    public $title = '积分兑换';
    public function __construct(&$app)
    {
        parent::__construct(app::get('b2c'));

    }

    public function exchange_coupon($coupon_id){
        if($coupon_id){
            $redirect_here = array('app' => 'integralexchange','ctl' => 'mobile_exchange','act' => 'exchange_coupon');
            $redirect_mycoupon = array('app' => 'b2c','ctl' => 'mobile_member','act' => 'coupon');
            $this->begin($redirect_mycoupon);//事务开始
            if(!vmc::singleton('b2c_coupon_stage')->exchange($coupon_id,$this->member['member_id'],$msg)){
                $this->end(false,'积分兑换优惠券失败,'.$msg,$redirect_here);//事务回滚
            }else{
                $this->end(true,'积分兑换优惠券成功');//事务提交
                return;
            }
        }

        /**
         * 优惠券列表
         */
        $this->title = '积分兑换优惠券';
        $current_time = time();
        $mdl_coupons = app::get('b2c')->model('coupons');
        $current_member = $this->member;
        $current_member_lv = $current_member['member_lv'];
        //获得可兑换优惠券列表
        $exchange_coupon_list = $mdl_coupons->getlist_exchange();
        foreach ($exchange_coupon_list as &$item) {
            $member_lv_ids = explode(',',$item['member_lv_ids']);
            if(!in_array($current_member_lv,$member_lv_ids)){
                $item['warning'][] = '会员当前等级不在优惠券限定范围';
                //todo 提示等级限制
            }
            if($current_time<$tiem['from_time'] || $current_time>$item['to_time']){
                $item['warning'][] = '请注意优惠券使用时间段';
            }
            if($current_member['integral']<$item['cpns_point']){
                $item['warning'][] = '积分余额不够兑换该优惠券';
            }

        }
        $this->pagedata['coupon_list'] = $exchange_coupon_list;
        $this->pagedata['member'] = $current_member;
        /**
         * @param appid
         */
        $this->output('integralexchange');
    }

}
