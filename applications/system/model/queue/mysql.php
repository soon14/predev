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




class system_mdl_queue_mysql extends base_db_model {

    function __construct($app){
        parent::__construct($app);
        $this->db->exec('set SESSION autocommit=1;');
        $this->db->exec('set @msgID = -1;');
    }

    /**
     * 获取一个队列任务信息
     *
     * @param string $queue_name 队列名称
     *
     * @return array $row
     */
    public function get($queue_name){
        $time = time();
        $sql = 'UPDATE vmc_system_queue_mysql force index(PRIMARY) SET owner_thread_id=GREATEST(CONNECTION_ID() ,(@msgID:=id)*0),last_cosume_time=\''.$time.'\' WHERE queue_name=\''.$queue_name.'\' and owner_thread_id=-1 order by id LIMIT 1;'; /*  */
        if ($this->db->exec($sql)){
            $this->db->beginTransaction();
            $sql = 'select id, worker, params from vmc_system_queue_mysql where id=@msgID';
            $row = $this->db->selectrow($sql);
            $this->db->commit();
            return $row;
        }

        return false;
    }

    function modifier_owner_thread_id($col){
        if($col == '-1'){
            return '等待执行...';
        }else{
            return '<span class="label label-default">'.$col.'</span> 执行中...';
        }
    }

    /**
     * 清空一个队列的内容
     *
     * @param string $queue_name
     *
     * @return bool
     */
    public function purge($queue_name){
        return $this->delete(array('queue_name' => $queue_name));
    }

    public function is_end($queue_name){
        if (!$this->getRow('id', array('queue_name' => $queue_name, 'owner_thread_id' => -1))){
            return true;
        } else {
            return false;
        }
    }
}
