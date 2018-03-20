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



class system_queue_consumer_proc_thread {

    /**
     * 句柄
     * @var resource
     */
    public $resource;

    /**
     * 管道
     * @var resource
     */
    public $pipes;

    /**
     * 脚本开始执行时间
     * @var Integer
     */
    private $startTime;


    /**
     * 析构
     *
     * @param string $queue_name PHP执行文件名
     * @param string $phpExec PHP脚本名
     * @return void
     */
    function __construct($queue_name,$phpExec='') {

        if ($phpExec){
            $executable = $phpExec;
        }elseif(strtoupper(substr(PHP_OS,0,3))=="WIN"){
            $executable = dirname(ini_get('extension_dir')).'/php';
            $executable = file_exists($executable) ? $executable : 'php';
        }else{
            $executable = PHP_BINDIR.'/php';
            $executable = file_exists($executable) ? $executable : 'php';
        }

        $script = PROCESS_DIR."/queue/queuescript.php";

        $descriptorspec = array(
                0 => array('pipe', 'r'),
                1 => array('file', '/dev/null', 'a'),
                2 => array('pipe', '/dev/null', 'a')
        );
        echo $executable." ".$script." ".$queue_name."\n";

        $i = 0;
        while (($this->resource = proc_open($executable." ".$script." ".$queue_name, $descriptorspec, $this->pipes, NULL, $_ENV))===null) {
            $i++;
            if ($i>2) {
                throw new Exception(' cannot create new proccess for consume queue.', 30001);
            }
        }

        $this->startTime = time();
    }

    /**
     * 检查任务是否运行中
     *
     * @param void
     * @return boolean
     */
    function isRunning() {

        $status = proc_get_status($this->resource);
        return $status["running"];
    }

    /**
     * 检查运行是否超时
     *
     * @param void
     * @return boolean
     */
    function isOverExecuted($max_exec_time) {

        if (($this->startTime + $max_exec_time) < time())
            return true;
        else
            return false;
    }
}
