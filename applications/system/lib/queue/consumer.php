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




class system_queue_consumer{
    
    private static $__instance = NULL;
    private $__consumer = NULL;
    
    
    function __construct($mode){
        //$consumer = vmc::singleton("system_queue_consumer_".$mode);
        $class = 'system_queue_consumer_'.$mode;
        $consumer = new $class;
        if($consumer instanceof system_interface_queue_IConsumer){
            $this->__consumer = &$consumer;
        }else{
            throw new Exception("The consumer must implements system_interface_queue_IConsumer!");
        }
    }
    
    /**
     * 如果没有定义QUEUE_CONSUMER常量，默认使用fork方式，如果系统不支持fork，可以使用proc模式
     * 
     * @param string $mode 消费者的模式，可选值：fork/proc
     * 
     * @return mixed 消费者对象
     */
    public static function instance($mode=NULL){
        if(empty($mode)){
            if(defined("QUEUE_CONSUMER") && constant("QUEUE_CONSUMER")){
                $mode = QUEUE_CONSUMER;
            }else{
                $mode = 'fork';
            }
        }
        
        if(!isset(self::$__instance[$mode])){
            if($instance = new system_queue_consumer($mode)){
                self::$__instance[$mode] = $instance;
                return self::$__instance[$mode];
            }else{
                return false;
            }
        }else{
            return self::$__instance[$mode];
        }
    }
    
    /**
     * 执行具体的任务
     * 
     * @param string $queue_name 队列名称
     * @param int $max 最大可开启的进程数
     * @param String $phpExec PHP脚本路径
     */
    public function exec($queue_name,$max=5,$phpExec=''){
        $this->__consumer->exec($queue_name,$max,$phpExec);
    }
}