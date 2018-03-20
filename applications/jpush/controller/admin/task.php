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


class jpush_ctl_admin_task extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
        $this->app = $app;
    }

    public function index()
    {
        $this->finder('jpush_mdl_task', array(
            'title' => ('推送任务'),
            'use_buildin_filter' => true,
            'actions' => array(
                array(
                    'label' => ('新建推送任务'),
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=jpush&ctl=admin_task&act=edit',
                ),
            ),
        ));
    }

    public function edit()
    {
        $this->page('admin/task/edit.html');
    }

    public function create()
    {
        $this->begin("index.php?app=jpush&ctl=admin_task&act=index");
        $mdl_task = $this->app->model('task');
        $task_params = $_POST;
        if (isset($_POST['event_params']['url']) && trim($_POST['event_params']['url']) != '') {
            $task_params['extras'] = array(
                'event_type' => 'push',
                'event_params' => $_POST['event_params'],
            );
        }
        $res = vmc::singleton('jpush_stage')->create_task($task_params, $err_msg);
        if (!$res) {
            $this->end(false,'推送任务创建失败!'.$err_msg);
        } else {
            $task_save_data = $task_params;
            $res = $res['body'];
            if($res['schedule_id']){
                $task_save_data['schedule_id'] = $res['schedule_id'];
                $task_save_data['schedule_enabled'] = 'true';
            }elseif($res['msg_id']){
                $task_save_data['msg_id'] = $res['msg_id'];
            }else{
                $this->end(false,'推送任务创建失败!'.$res['error']['message']);
            }
            $task_save_data['createtime'] = time();
            if (!empty($task_save_data['send_time'])) {
                $task_save_data['send_time'] = strtotime($task_save_data['send_time']);
            }
            if(!is_numeric($task_save_data['send_time'])){
                $task_save_data['send_time'] = time();
            }
            if ($task_save_data['extras']) {
                $task_save_data['event_type'] = $task_save_data['extras']['event_type'];
                $task_save_data['event_params'] = $task_save_data['extras']['event_params'];
            }else{
                unset($task_save_data['event_params']);
            }
            $this->end($mdl_task->save($task_save_data));
        }

    }
}
