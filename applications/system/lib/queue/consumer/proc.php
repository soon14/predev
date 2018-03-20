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



class system_queue_consumer_proc implements system_interface_queue_IConsumer{

    /**
     * 当前运行的线程数
     * @var Integer
     */
    private $threadRunning = 0;


    /**
     * 运行中的线程对象
     * var Array
     */
    private $running = array();
    
    
    /**
     * 子进程最大可执行时间，单位：秒，默认1小时
     * @var int
     */
    private $max_exec_time = 3600;

    /**
     *  
     * @var int
     */
    
    private $error_times = 0;
     
    
    /**
     * 设置子进程最大可执行时间，单位：秒
     *
     * @param int $sec
     */
    public function setMaxExecTime($sec){
        $this->max_exec_time = $sec;
    }
    
    /**
     * 获取子进程最大可执行时间，单位：秒
     *
     * @return int $sec
     */
    public function getMaxExecTime(){
        $sec = $this->max_exec_time;
        return $sec;
    }


    /**
     * 多进程执行队列
     *
     * @param string $queue_name
     * @param int $max
     * @param string $phpExec
     */
    public function exec($queue_name,$max,$phpExec=''){
        $max = $max ? $max : 1;
        
        $time = time();
        while(1) {
            //执行死循环
            try {
                while ($this->threadRunning < $max && !system_queue::instance()->is_end($queue_name)) {
                    $this->running[] = new system_queue_consumer_proc_thread($queue_name,$phpExec);
                    usleep(200000);
                    $this->threadRunning++;
                }
            }
            catch (Exception $e) {
                switch($e->getCode()) {
                case 30001:
                    logger::emerg(sprintf('ERROR:%d @ %s', $e->getCode(), $e->getMessage));
                    exit;
                }
            }

            //检查是否已经结束
            if ($this->threadRunning == 0) {

                break;
            }

            //等待代码执行完成
            usleep(50000);

            $thread_close = array();//记录线程的关闭状态

            //检查已经完成的任务
            foreach ($this->running as $idx => $thread) {

                if (!$thread->isRunning() || $thread->isOverExecuted($max)) {
                    $thread_close[] = proc_close($thread->resource);//记录线程的关闭状态
                    unset($this->running[$idx]);
                    $this->threadRunning--;
                }
            }
        }
    }

}