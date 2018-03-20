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




class system_queue{

    static private $__instance = null;

    static private $__config = null;

    private $__controller = null;

    static private function __init() {
        if (!isset(self::$__config)) {
            require(ROOT_DIR.'/config/queue.php');
            self::$__config['queues'] = $queues;
            self::$__config['bindings'] = $bindings;
        }
    }

    static public function get_config($key=null){
        if (!is_null($key)) {
            return self::$__config[$key];
        }
        return self::$__config;

    }

    public function __construct(){
        self::__init();
        $controller = self::get_controller_name();
        $this->set_controller(new $controller);
    }

    static function get_controller_name(){
        if(defined('QUEUE_SCHEDULE')){
            $controller = constant('QUEUE_SCHEDULE');
        }else{
            $controller = 'system_queue_adapter_mysql';
        }
        return $controller;
    }

    public function get_controller(){
        return $this->__controller;
    }

    public function set_controller($controller){
        if ($controller instanceof system_interface_queue_adapter) {
            $this->__controller = $controller;
        }else{
            throw new Exception('this instance must implements system_interface_queue_adapter');
        }
    }

    static public function get_queue($queue_name){
        if (isset(self::$__config['queues'][$queue_name])) {
            return self::$__config['queues'][$queue_name];
        }
        return false;
    }

    static public function get_exchange($exchange_name){
        if (isset(self::$__config['exchanges'][$exchange_name])) {
            return self::$__config['exchanges'][$exchange_name];
        }
        return false;
    }

    static public function get_queues() {
        return self::$__config['queues'];
    }

    static public function get_bindings(){
        return self::$__config['bindings'];
    }

    static public function instance(){
        if (!isset(self::$__instance)) {
            self::$__instance = new system_queue;
        }
        return self::$__instance;
    }

    static private function __get_publish_queues($exchange_name){
        if (!isset(self::$__config['bindings'][$exchange_name])){
            return array(DEFAULT_PUBLISH_QUEUE);
        }
        return self::$__config['bindings'][$exchange_name];
    }

    public function publish($exchange_name, $worker, $params=array(), $routing_key=null){
        $queues = $this->__get_publish_queues($exchange_name);
        foreach($queues as $queue_name){
            $queue_data = array(
                'queue_name' => $queue_name,
                'worker' => $worker,
                'params' => $params);

            $this->get_controller()->publish($queue_name, $queue_data);
        }
        return true;
    }


    public function get($queue_name){
        $queue_message = $this->get_controller()->get($queue_name);
        if ($queue_message instanceof system_interface_queue_message) {
            return $queue_message;
        }
        return false;
    }

    public function ack($queue_message){
        $this->get_controller()->ack($queue_message);
    }


    public function run_task($queue_message){
        //todo: 异常处理
        $worker = $queue_message->get_worker();
        $params = $queue_message->get_params();

        $obj_task = new $worker();
        if ($obj_task instanceof base_interface_task) {
            call_user_func_array(array($obj_task, 'exec'), array($params));
            logger::info('task:'. get_class($obj_task). ' exec ok');
        }
        return true;
    }

    public function purge($queue_name){
        $this->get_controller()->purge();
    }

    public function is_end($queue_name){
        return $this->get_controller()->is_end($queue_name);
    }

    static public function write_config(){
        return utils::cp(ROOT_DIR.'/app/base/examples/queue.php', ROOT_DIR.'/config/queue.php');
    }

}
