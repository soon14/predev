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
class marketing_tasks_members extends base_task_abstract implements base_interface_task
{
    public function exec($params=null){
        $group = app::get('marketing') ->model('group')->dump($params['group_id']);
        $order_filter = $this ->order_status_filter($group['order_status']);
        $order_filter['createtime|between'] =array($group['from_time'] ,$group['to_time']);
        $message_from_time = strtotime('-6 months');
        if($group['conditions']['email_succ']=='true' && $group['conditions']['email_sms']=='true'){
            $order_filter['filter_sql'] = "member_id in (select member_id from vmc_marketing_message where createtime>$message_from_time)";
        }elseif($group['conditions']['email_succ']=='true'){
            $order_filter['filter_sql'] = "member_id in (select member_id from vmc_marketing_message where message_type='email' and createtime>$message_from_time)";
        }elseif($group['conditions']['sms_succ']=='true'){
            $order_filter['filter_sql'] = "member_id in (select member_id from vmc_marketing_message where message_type='sms' and createtime>$message_from_time)";
        }
        if(!empty($group['conditions']['area'])){
            $province = implode(',', $group['conditions']['area']);
            $order_filter['filter_sql'] .= ($order_filter['filter_sql'] ? ' AND ':'')."member_id in (SELECT member_id FROM `vmc_b2c_member_addrs` WHERE is_default='true' and province in ({$province}))";
        }
      
        $mdl_orders = app::get('b2c') ->model('orders');
        $group['order_filter'] = $filter = $mdl_orders ->_filter($order_filter);
        $db = vmc::database();
        $sql = 'select member_id ,sum(order_total) as order_sum ,count(1) as order_count ,sum(quantity) as order_items_quantity from vmc_b2c_orders where '.$filter.' group by member_id';
        $count = 1000000;
        $limit =200;
        $step = ceil($count/$limit);
        $conditions  = $group['conditions']['conditions'];
        $condition_obj = vmc::singleton('marketing_condition');
        $member_group = app::get('marketing') ->model('group_members');
        $member_nums = 0;
        for($i=0;$i<$step ;$i++){
            $order_sql = $sql.' limit '.$i*$limit.','.$limit;
            $rows = $db ->select($order_sql);
            if(empty($rows)){
                break;
            }
            foreach($rows as $row){
                if(!$row['member_id']){
                    continue;
                }
                $is_meet = true;
                foreach($conditions['match'] as $match_condition){
                    if(!$condition_obj ->is_meet($match_condition ,$row ,$group)){
                        $is_meet =false;
                        break;
                    }

                }
                if(!$is_meet){
                    continue;
                }
                foreach($conditions['no_match'] as $match_condition){
                    if($condition_obj ->is_meet($match_condition ,$row ,$group)){
                        $is_meet =false;
                        break;
                    }
                }
                if(!$is_meet){
                    continue;
                }
                $member =array(
                    'member_id' =>$row['member_id'],
                    'group_id' =>$group['group_id']
                );
                if(!$member_group ->count($member)){
                    $member_group ->save($member);
                }
                $member_nums++;
            }
        }

        //TODO 将包含的分组会员去重记录
        if($group['conditions']['group_id']){
            $group_member_filter = array(
                'group_id'=>$group['conditions']['group_id'],
                'filter_sql'=>'member_id NOT IN (SELECT member_id FROM `vmc_marketing_group_members` WHERE group_id='.$group['group_id'].')'
            );
            $count = $member_group ->count($group_member_filter);
            $limit =200;
            $step = ceil($count/$limit);
            for($i=0 ; $i<$step ;$i++){
                $rows = $member_group ->getList('member_id' ,$group_member_filter ,0 ,$limit);
                if($rows){
                    foreach($rows as $row){
                        $member =array(
                            'member_id' =>$row['member_id'],
                            'group_id' =>$group['group_id']
                        );
                        if(!$member_group ->count($member)){
                            $member_group ->save($member);
                        }
                        $member_nums++;
                    }
                }
            }
        }

        app::get('marketing') ->model('group')->update(array('nums'=>$member_nums,'status'=>'1'), array('group_id' =>$group['group_id']));

        return true;
    }


    private function order_status_filter( $status){
        switch($status){
            case '0':
                return false;
            case '1':
                return array('status'=>'active','pay_status'=>'0');
            case '2':
                return array('status'=>'active','ship_status'=>'0');
            case '3':
                return array('status'=>'active','ship_status'=>'1');
            case '4':
                return array('status'=>'dead');
            case '5':
                return array('status'=>'finish');
            default:
                return false;
        }
    }
}