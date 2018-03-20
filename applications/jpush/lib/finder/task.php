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


class jpush_finder_task
{
    public $detail_task = '推送任务详情';
    public $column_schedule = '是否立即推送';

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function detail_task($task_id)
    {

        $mdl_task = $this->app->model('task');
        $task = $mdl_task->dump($task_id);
        $render = $this->app->render();
        if($task['msg_id']){
            $report = vmc::singleton('jpush_stage')->report($task['msg_id'],$error_msg);
            $render->pagedata['msg_report'] = $report;
        }
        return $render->fetch('admin/task/detail.html');
    }

    public function column_schedule($row){
        if($row['@row']['schedule_id'] && $row['@row']['schedule_enabled'] == 'true'){
            return "<span class='label label-success'><i class='fa fa-clock-o'></i> 定时</span>";
        }else{
            return "是";
        }
    }


    public function row_style($row)
    {
        //$row = $row['@row'];
    }
}
