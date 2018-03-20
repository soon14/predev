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


class preselling_mdl_activity extends dbeav_model{

    var $has_tag = true;
    public $defaultOrder = array(
        'w_order DESC,activity_id DESC',
    );
    
    public function get_member_lv($member_id) {
        if($member_id) {
            if($member_lv_row = app::get('pam')->model('account')->db->selectrow('select member_lv_id from vmc_b2c_members where member_id='.intval($member_id))) {
                return $member_lv_row['member_lv_id'];
            };
        }
        return -1;
    }

    /**
     * 有未预售订单不可以删除该活动
     * @param array post
     * @return boolean
     */
    public function pre_recycle($rows)
    {
        $this->recycle_msg = '删除成功!';
        /*$activity_ids = array_keys(utils::array_change_key($rows, 'activity_id'));
        if(app::get('preselling')->model('orders')->count(array('status' => '0','is_failure'=>'0', 'activity_id' => $activity_ids))) {
            $this->recycle_msg = '活动id：'.implode(',', $activity_ids).'包含未预售订单';
            return false;
        }*/
        return true;
    }

}