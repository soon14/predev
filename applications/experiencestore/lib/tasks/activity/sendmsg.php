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


class experiencestore_tasks_activity_sendmsg extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        $mdl_orders = app::get('experiencestore')->model('activity_order');
        $mdl_schedule = app::get('experiencestore')->model('activity_schedule');
        $now = time();
        $before = 3600*24*4;
        $schedule = $mdl_schedule ->getList('*' ,array('from_time|than'=>$now ,'from_time|sthan'=>$now+$before));
        if($schedule){
            $filter = array(
                'has_notice' =>'0', //未提醒
                'filter_sql' =>'`ticket_price` =`payed`',
            );
            $mdl_store = app::get('experiencestore')->model('store');
            $mdl_subject = app::get('experiencestore')->model('activity_subject');
            foreach($schedule as $k =>$v){
                $v['store'] =$mdl_store ->getRow('*' ,array('id' =>$v['store_id']));
                $v['subject'] =$mdl_subject ->getRow('*' ,array('id' =>$v['subject_id']));
                $filter['schedule_id'] = $v['id'];
                $step = 100;
                $count = $mdl_orders ->count($filter);
                if($count){
                    $nums = ceil($count /$step);
                    for($i=0;$i<$nums ;$i++){
                        $orders = $mdl_orders ->getList("*" , $filter ,$step*$i ,$step);
                        if($orders){
                            $this ->send_msg($v , $orders);
                            $mdl_orders ->update(array('has_notice' =>'1' ,array('id' =>array_keys(utils::array_change_key($orders,'id')))));
                        }
                    }
                }

            }
        }

        return true;
    }

    private function send_msg($schedule ,$order_list){
        foreach($order_list as $k=>$v){
            $user_object = vmc::singleton('b2c_user_object');
            if(!$v['phone']){
                $pam_data = $user_object->get_pam_data('*',$v['member_id']);
            }
            $env_list = array(
                'activity_order' =>$v['id'],
                'store_name' =>$schedule['store']['name'],
                'store_address' =>$schedule['store']['address'],
                'subject_title'=>$schedule['subject']['title'],
                'code'=>$schedule['code'],
                'from_time'=>date('Y-m-d H:i:s',$schedule['from_time']),
                'to_time'=>date('Y-m-d H:i:s',$schedule['to_time']),
            );
            vmc::singleton('b2c_messenger_stage')->trigger('alert_activity',$env_list,array(
                'email'=>$pam_data['email']['login_account'],
                'mobile'=>$v['phone'] ?$v['phone']:$pam_data['mobile']['login_account'],
                'member_id'=>$order_list['member_id']
            ));
        }
    }
}
