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




class system_ctl_admin_queue extends desktop_controller {


    function index() {
        $params = array (
            'title' => '队列管理',
            'use_buildin_recycle'=>true,
        );
        $queue_controller_name = system_queue::get_controller_name();
        $support_queue_controller_name = 'system_queue_adapter_mysql';

        if ($queue_controller_name == $support_queue_controller_name) {
            $this->finder('system_mdl_queue_mysql', $params);
        }else{
            $this->pagedata['queue_controller_name'] = $queue_controller_name;
            $this->pagedata['support_queue_controller_name'] = $support_queue_controller_name;

            $this->page('admin/queue.html');
        }
    }

    function retry($task_id){
        $this->begin();
        $queue_controller_name = system_queue::get_controller_name();
        $support_queue_controller_name = 'system_queue_adapter_mysql';
        if ($queue_controller_name == $support_queue_controller_name) {
            $mdl_queue_mysql = $this->app->model('queue_mysql');
            $task = $mdl_queue_mysql->dump($task_id);
            try{
                $params = $task['params'];
                if(!is_array($params)){
                    $params = unserialize($params);
                }
//*******
                //判断如果传过来的是vmcconnect_tasks_hook_queue_warning或者vmcconnect_tasks_api_queue_warning
                if ($task['worker'] == 'vmcconnect_tasks_hook_queue_warning' || $task['worker'] == 'vmcconnect_tasks_api_queue_warning'){
                    $task['worker'] = 'vmcconnect_tasks_queue_warning';
                }
//*******

                vmc::singleton($task['worker'])->exec($params);
            }catch(Exception $e){
                $exception_msg = $e->getTrace().$e->getMessage();
                $mdl_queue_mysql->update(array('exception_msg'=>$exception_msg),array('id'=>$task_id));
                $this->end(false,'出现异常！');
                //如果执行失败，则添加到hook执行日志内，显示为fail
                vmc::singleton('vmcconnect_tasks_base')->exception_handling($params);
            }
            //如果执行成功，需要将警报解除掉
            //判断执行的method是否在failure表中
            $fail_model = app::get('vmcconnect')->model('failure_count');
            $fail_methods = $fail_model->getlist('item_id',array('worker'=>$task['worker']));
            if ($fail_methods){
                $data['remove'] = 'true';
                foreach ($fail_methods as $k=>$v){
                    $data['item_id'] = $v['item_id'];
                    $data['failure_count'] = 0;
                    $data['warning_count'] = 0;
                    $fail_model->save($data);
                }
            }

            $mdl_queue_mysql->delete(array('id'=>$task_id));
            $this->end(true,'重试成功!');
        }else{
            $this->end(false,'暂不支持');
        }
    }

    //添加再次执行按钮
    function send_again($task_id){

        $this->begin();
        //按照task_id获取对应参数
        $hooktask_item_obj = app::get('vmcconnect')->model('hooktask_items');
        $task = $hooktask_item_obj->dump($task_id);

        try{
            $params = array('task_id' => $task['task_id']);
            vmc::singleton('vmcconnect_tasks_'.$task['task_type'])->exec($params);

        }catch(Exception $e){
            $exception_msg = $e->getTrace().$e->getMessage();
            $hooktask_item_obj->update(array('exception_msg'=>$exception_msg,'act_res'=>0),array('item_id'=>$task_id));
            $this->end(false,'出现异常！');
            //进入异常处理
            vmc::singleton('vmcconnect_tasks_base')->exception_handling($params, $task);

        }
        //执行成功后将exception_msg异常信息清空，执行更新send_date
        $new_send_date = time();
        $hooktask_item_obj->update(array('exception_msg'=>'','send_date' => $new_send_date,'act_res'=>1),array('item_id'=>$task_id));
        //如果此方法执行成功，则表示异常排除，需解除警报并将异常清零
        $fail_model = app::get('vmcconnect')->model('failure_count');
        $fail_methods = $fail_model->getlist('item_id',array('worker'=>'vmcconnect_tasks_'.$task['task_type']));
        if ($fail_methods){
            $data['remove'] = 'true';
            foreach ($fail_methods as $k=>$v){
                $data['item_id'] = $v['item_id'];
                $data['failure_count'] = 0;
                $data['warning_count'] = 0;
                $fail_model->save($data);
            }
        }

        $task_id = array('item_id'=>$task_id);
        $hooktask_item_obj->delete($task_id);
        $this->end(true,'再次执行成功!');
    }
}
