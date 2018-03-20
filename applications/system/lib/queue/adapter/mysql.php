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




class system_queue_adapter_mysql implements system_interface_queue_adapter{
    private $__model = NULL;
    
    function __construct(){
        
        $this->__model = app::get('system')->model('queue_mysql');
    }
    
    /**
     * 添加一个队列任务
     *
     * @param string $queue
     * @param array $queue_data
     * 
     * @return bool
     */
    public function publish($queue_name,$queue_data){
        $time = time();
        $data = array('queue_name' => $queue_data['queue_name'],
                      'worker' => $queue_data['worker'],
                      'params' => serialize((array)$queue_data['params']),
                      'create_time' => $time);

        return $this->__model->insert($data);
    }
    
    /**
     * 获取一个队列任务ID
     * @param string $queue 队列名称
     *
     * @return mixed 队列任务数据
     */
    public function get($queue_name){
        if (($row = $this->__model->get($queue_name))){
            $queue_data = array(
                'id' => $row['id'],
                'params' => unserialize($row['params']),
                'worker' => $row['worker']);
            return new system_queue_message_mysql($queue_data);
        }
        return false;
    }

    /**
     * 确认消息已经被消费. 
     * @param string $queue_id 队列id号
     *
     * @return mixed 队列任务数据
     */
    public function ack($queue_message){
        $queue_id = $queue_message->get_id();
        return $this->__model->delete(array('id'=>$queue_id));
    }


    /**
     * 清空一个队列 
     *
     * @param string $queue
     */
    public function purge($queue_name){
        return $this->__model->purge($queue_name);
    }

    public function is_end($queue_name){
        return $this->__model->is_end($queue_name);
    }

    public function consume($queue_name){
    }

}

