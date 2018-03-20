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

class vmcconnect_services_queue_warning extends vmcconnect_services_base
{
    public function exec($data, $queue_data) {

        //判断来源
        switch ($queue_data['type']){
            case 'hook':
                $params = array('skus' => $data);
                $task_name = 'queue_warning';
                $publish_name = 'vmcconnect_tasks_hook_'.$task_name;

                $task_id = $data['task_id'];

                $alert_phone = $phone;
                system_queue::instance()->publish(
                    $publish_name,
                    $publish_name,
                    array(
                        'type' => 'hook',
                        'task_id' => $task_id,
                        'alert_phone'=>$queue_data['alert_phone'],
                        'worker'=>$queue_data['worker'],
                    ));
                break;
            case 'api':
//                $params = array('skus' => $data);
                $task_name = 'queue_warning';
                $publish_name = 'vmcconnect_tasks_api_'.$task_name;

                $task_id = $data['logs_id'];

//                $alert_phone = $phone;
                system_queue::instance()->publish(
                    $publish_name,
                    $publish_name,
                    array(
                        'type' => 'api',
                        'logs_id' => $task_id,
                        'alert_phone'=>$queue_data['alert_phone'],
                        'worker'=>$queue_data['worker'],
                    ));
                break;
        }


        return true;

    }

}