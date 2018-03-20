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
class marketing_tasks_send extends base_task_abstract implements base_interface_task
{
    public function exec($params=null){
        $current_time = time();
        $task_mdl =app::get('marketing') ->model('message_tasks');
        $group_mdl =app::get('marketing')->model('group');
        $group_members_mdl = app::get('marketing') ->model('group_members');
        $filter = $params['task_id'] ? array('task_id'=>$params['task_id']) :array('send_time|between' => array(1,$current_time));
        $filter['send_status'] = '0';
        $tasks = $task_mdl ->getList('*' ,$filter);

        if(!$tasks){
            return true;
        }
        $report_mdl = app::get("marketing") ->model('report');
        foreach($tasks as $task){
            $task['send_status'] = '1';
            if(!$task_mdl ->save($task)){
                logger::error('营销任务状态更新失败'.var_export($task ,1));
            }
            if(!$report_mdl ->update(array('send_time'=>time()) ,array('task_id'=> $task['task_id']))){
                logger::error('营销效果报告记录失败');
                return false;
            }
            $group_id = $task['group_id'];
//            $group =$group_mdl ->getRow('*' ,array('group_id' =>$group_id));
//            if($group['conditions']['group_id']){
//                $group_id = array_merge((array)$group_id ,(array)$group['conditions']['group_id']);
//            }
            $members_count =$group_members_mdl ->count(array('group_id' =>$group_id));
            $limit =500;
            $step = ceil($members_count/500);
            for($i=0;$i<$step ;$i++){
                $rows = $group_members_mdl ->getList('*' ,array('group_id' =>$group_id) ,$i*$limit ,$limit);
                $data =array(
                    'member_id'=>array_keys(utils::array_change_key($rows ,'member_id')),
                    'group_id' =>$group_id,
                    'task' =>$task,
                    'all' => $i+1 == $step ?true :false
                );
                system_queue::instance()->publish('marketing_tasks_batchsend', 'marketing_tasks_batchsend', $data);
            }

        }
        return true;
    }
}