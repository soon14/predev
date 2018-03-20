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
class digitalmarketing_mdl_win extends dbeav_model{
    public function modifier_member_id($row)
    {
        if ($row === 0 || $row == '0'){
            return ('非会员顾客');
        }
        else{
            return vmc::singleton('b2c_user_object')->get_member_name(null,$row);
        }
    }

    public function modifier_prize_detail($col,$row)
    {
        if(!$row['prize_type']) {
            $row = $this ->dump($row['win_id']);
        }
        if($row['prize_type']=='coupon'){
            return $col['cpns_name'].'优惠券';
        }elseif($row['prize_type']=='product'){
            return $col['name'];
        }
        return $col['name'];
    }

    public function modifier_order_id($col,$row)
    {
        if(!$col) {
            return '';
        }
        return '<a target="_blank" href="index.php?app=b2c&ctl=admin_order&act=detail&p[0]='.$col.'" > '.$col.'</a>';;
    }
}