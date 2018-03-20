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
class experiencestore_tasks_activity_finish extends base_task_abstract implements base_interface_task
{
    public function exec($params=null){
        $filter =array(
            'status' =>'active',
        );
        $schedule_mdl =app::get('experiencestore') ->model('activity_schedule');
        $schedule = $schedule_mdl ->getList('id' ,$filter);
        if($schedule){
            foreach($schedule as $v){
                if($v['to_time']<time()){
                    $schedule_mdl ->update(array('status' =>'finish') ,array('id' =>$v['id']));
                    foreach(vmc::servicelist('experiencestore.activity.finish') as $obj){
                        $obj ->exec(array('schedule_id' =>$v['id']));
                    }
                }
            }
        }
        return true;
    }
}