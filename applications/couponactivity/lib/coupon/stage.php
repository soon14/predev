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


class couponactivity_coupon_stage
{
    public function __construct()
    {
        $this->app = app::get('couponactivity');
    }

    /**
     * 优惠券领取.
     *
     * @param $cid int 优惠券ID
     * @param $id int 活动ID
     * @param $m_id int 会员ID
     * @param &$msg 提示信息
     */
    public function get_cpns($cid,$id=0,$m_id=0,&$msg=''){
        if(!$cpns_id = $cid ){
            $msg = '参数错误';
            return false;
        }
        $member_id = $m_id>0?$m_id:vmc::singleton('b2c_user_object')->get_member_id();
        if(!$member_id){
            $msg = '会员信息有误';
            return false;
        }
        $mdl_activity = $this->app->model('activity');
        $filter = array(
            'status' =>  'true',
            'activity_id' => $id,
        );
        $activity = $mdl_activity->getRow('*',$filter);
        if(!$activity){
            $msg = '活动不存在';
            return false;
        }
        if( $activity['from_time']>time()||$activity['to_time']<time() ){
            $msg = '活动不不在有效期内';
            return false;
        }
        $mdl_activities = $this->app->model('activities');
        $filter = array(
            'activity_id' => $activity['activity_id'],
            'cpns_id' => $cpns_id,
            );
        $activities = $mdl_activities->getRow('*',$filter);
        if(!$activities){
            $msg = '活动优惠券不存在';
            return false;
        }
        $achieve_sum = $activities['achieve_sum']>0?$activities['achieve_sum']:0;
        $activities['achieve_sum'] = $achieve_sum + 1;
        if( !$mdl_activities->update($activities,$filter) ){
            $msg = '活动优惠券领取失败';
            return false;
        }

        $mdl_achieve = $this->app->model('achieve');
        if($activities['num_sum']>0){
            if($activities['num_sum']-$achieve_sum<1){
                $msg = '活动优惠券被抢光了';
                return false;
            }
        }
        if($activities['num']){
            $filter['member_id'] = $member_id;
            $achieve = $mdl_achieve->getRow('sum(num) as num',$filter);
            if($activities['num']-$achieve['num']<1){
                $msg = '活动优惠券已领完';
                return false;
            }
        }
        $cpns_mdl = app::get('b2c')->model('coupons');
        $coupon = $cpns_mdl->dump($cpns_id);
        if(!$coupon || $coupon['cpns_status'] == '0' ) {
            $msg = '优惠券被抢光了';
            return false;
        }
        $mdl_rule = app::get('b2c')->model('sales_rule_order');
        $rule_data = $mdl_rule->getRow('*', array('rule_id' => $coupon['rule']['rule_id']));
        if ($rule_data['to_time'] < time() || $rule_data['status'] == false) {
            $msg = '优惠券已过期了';
            return false;
        }
        // 判断是否已经领取过
        // $member_cpns = app::get('b2c') ->model('member_coupon')->count(array('cpns_id'=>$cpns_id ,'member_id'=>$member_id,'memc_enabled'=>'true','disabled'=>'false','memc_isvalid'=>'true','memc_used_times'=>0));
        // if($member_cpns >= 1){
        //     $msg = '您已经领取过该优惠券';
        //     return false;
        // }
        $list = $cpns_mdl->downloadCoupon($cpns_id, 1, '1', '用户领取', '用户领取');
        if(!$list){
            $msg = '优惠券被抢光了';
            return false;
        }
        $code = $list[0];
        $re = vmc::singleton('b2c_coupon_stage') -> isAvailable($member_id ,$code ,$msg);
        if(!$re){
            $msg = $msg?:'领取失败';
            return false;
        }

        $filter = array(
            'activity_id' => $activity['activity_id'],
            'cpns_id' => $cpns_id,
            'member_id' => $member_id
            );
        $achieve = $mdl_achieve->getRow('id,num',$filter);
        if( $achieve ){
            $achieve['num'] += 1;
            $res = $mdl_achieve->update($achieve,$filter);
        }else{
            $save = $filter;
            $save['num'] = 1;
            $save['createtime'] = time();
            $res = $mdl_achieve->insert($save);
        }
        if( !$res ){
            $msg = '领取信息保存失败！';
            return false;
        }

        $success_data = array(
            'name' => substr($rule_data['name'],strpos($rule_data['name'],'-')+1),
            'description' => $rule_data['description'],
            'from_time' => date('Y-m-d H:i:s',$rule_data['from_time']),
            'to_time' => date('Y-m-d H:i:s',$rule_data['to_time']),
            'cpns_no' => $code
        );
        $msg = '领取成功';
        return $success_data;
    }

    /**
     * 优惠券活动.
     *
     * @param $filter Array 活动筛选条件
     * @param $offset int 开始条数
     * @param $limit int 每次查找条数
     * @param $orderType String 排序条件
     */
    public function activity($filter)
    {
        if($filter['activity_id']<1){
            return false;
        }
        $mdl_activity = $this->app->model('activity');
        $mdl_coupons = app::get('b2c')->model('coupons');
        $mdl_activities = $this->app->model('activities');
        // 获取活动
        $filter['status'] = 'true';
        $filter['from_time|sthan'] = time();
        $filter['to_time|than'] = time();
        $activity = $mdl_activity->getRow('*',$filter);
        // 获取活动优惠券配置信息
        $act_cpns = $mdl_activities->getList( '*', array('activity_id'=>$filter['activity_id']) );
        $act_cpns = utils::array_change_key($act_cpns,'cpns_id');
        $cpns_ids = array_keys($act_cpns);
        // 获取可用优惠券信息
        $time = time();
        $cpns_sql = "select c.*,o.* from ".vmc::database()->prefix."b2c_coupons c inner join vmc_b2c_sales_rule_order o on (o.rule_id=c.rule_id and o.status='true') where c.cpns_status='1' and c.cpns_id in(".implode($cpns_ids,',').") and o.to_time>{$time}";
        $coupons = $mdl_coupons->db->select($cpns_sql);

        if( !$activity||!$act_cpns ){
            return false;
        }
        $activity['now_time'] = $activity['to_time']-$time;
        // 获取个人领取信息
        if( $member_id = vmc::singleton('b2c_user_object')->get_member_id() ){
            $mdl_achieve = $this->app->model('achieve');
            $filter_achieve = array(
                'activity_id' => $filter['activity_id'],
                'cpns_id' => $cpns_ids,
                'member_id' => $member_id,
            );
            $ach_list = $mdl_achieve->getList('*',$filter_achieve);
            $ach_list = utils::array_change_key($ach_list,'cpns_id');
        }
        // 计算可领取优惠券数量
        $cpns_list = array();
        $cpns = array();
        foreach($coupons as $v){
            $cpns[$v['cpns_id']] = $act_cpns[$v['cpns_id']];
            $cpns_list[$v['cpns_id']] = $v;
            $cpns_list[$v['cpns_id']]['is_buy'] = 'true';
            // 计算剩余总数
            if($act_cpns[$v['cpns_id']]['num_sum']>0){
                $num_sum = $act_cpns[$v['cpns_id']]['num_sum']-$act_cpns[$v['cpns_id']]['achieve_sum'];
                $cpns_list[$v['cpns_id']]['buy_sum'] = $num_sum;
                if(!$act_cpns[$v['cpns_id']]||$num_sum<1){
                    $cpns_list[$v['cpns_id']]['is_buy'] = 'false';
                    continue;
                }
            }
            // 计算个人可领取数
            if( $member_id>0 && $act_cpns[$v['cpns_id']]['num']>0){
                $num = $act_cpns[$v['cpns_id']]['num']-$ach_list[$v['cpns_id']]['num'];
                $cpns_list[$v['cpns_id']]['buy_num'] = $num;
                if($num<1){
                    $cpns_list[$v['cpns_id']]['is_buy'] = 'false';
                    continue;
                }
            }
        }
        return array(
            'activity' => $activity,
            'cpns' => $cpns,
            'cpns_list' => $cpns_list,
        );
    }





}
