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


class serveradm_status {
    private $_mysql_status = array(
        "Connections" => "",
        "Created_tmp_disk_tables" => "",
        "Created_tmp_files" => "",
        "Created_tmp_table" => "",
        //"Innodb_buffer_pool_pages_data"=>"",
        //"Innodb_buffer_pool_pages_dirty"=>"",
        //"Innodb_buffer_pool_pages_flushed"=>"",
        //"Innodb_buffer_pool_pages_free"=>"",
        //"Innodb_buffer_pool_pages_misc"=>"",
        //"Innodb_buffer_pool_pages_total"=>"",
        //"Innodb_buffer_pool_read_ahead_rnd"=>"",
        //"Innodb_buffer_pool_read_ahead_seq"=>"",
        //"Innodb_buffer_pool_read_requests"=>"",
        //"Innodb_buffer_pool_reads"=>"",
        //"Innodb_buffer_pool_wait_free"=>"",
        //"Innodb_buffer_pool_write_requests"=>"",
        //"Innodb_data_read"=>"",
        //"Innodb_data_reads"=>"",
        //"Innodb_data_writes"=>"",
        //"Innodb_data_written"=>"",
        //"Innodb_pages_read"=>"",
        //"Innodb_pages_written"=>"",
        //"Innodb_row_lock_current_waits"=>"",
        //"Innodb_row_lock_time"=>"",
        "Innodb_row_lock_time_avg" => "",
        "Innodb_row_lock_time_max" => "",
        //"Innodb_row_lock_waits"=>"",
        //"Key_blocks_used"=>"",
        //"Key_read_requests"=>"",
        //"Key_reads"=>"",
        //"Key_write_requests"=>"",
        //"Key_writes"=>"",
        //"Open_files"=>"",
        //"Open_table_definitions"=>"",
        //"Open_tables"=>"",
        //"Opened_files"=>"",
        //"Opened_table_definitions"=>"",
        //"Opened_tables"=>"",
        //"Qcache_free_blocks"=>"",
        //"Qcache_free_memory"=>"",
        //"Qcache_hits"=>"",
        //"Qcache_lowmem_prunes"=>"",
        //"Qcache_not_cached"=>"",
        //"Qcache_queries_in_cache"=>"",
        //"Qcache_total_blocks"=>"",
        "Select_full_join" => "",
        "Select_full_range_join" => "",
        //"Select_range"=>"",
        //"Select_range_check"=>"",
        "Slow_queries" => "",
    );
    /**
     * 构造方法，初始化此类的某些对象
     * @param object 此应用的对象
     * @return null
     */
    public function __construct(&$app) {
        $this->app = $app;
    }
    public function getCacheInfo() {
        if (!defined("CACHE_ADAPTER")) return false;
        $aTmp = explode("_", CACHE_ADAPTER);
        $msg = false;
        cachemgr::status($msg);
        $aResult = array(
            "name" => $aTmp[count($aTmp) - 1],
            "status" => $msg,
        );
        return $aResult;
    }
    public function getKVStorageInfo() {
        if (!defined("KV_ADAPTER")) return false;
        $aTmp = explode("_", KV_ADAPTER);
        $aResult = array(
            "name" => $aTmp[count($aTmp) - 1],
        );
        return $aResult;
    }
    public function getMysqlStatus() {
        $db = vmc::database();
        $aStatus = $db->select("show status");
        $aShowStaus = array_keys($this->_mysql_status);
        $aResult = array();
        // 可以进行分组咯
        foreach ($aStatus as $row) {
            if (in_array($row["Variable_name"], $aShowStaus)) $aResult[$row["Variable_name"]] = $row["Value"];
        }
        return $aResult;
    }
    public function getXHProfStatus() {
        if (!extension_loaded('xhprof')) return false;
        // 再写咯
        return 'XHProf';
    }
    public function getServerInfo() {
        return array(
            array(
                'name' => "PHP版本" ,
                'value' => PHP_VERSION,
            ) ,
            array(
                'name' => "php运行方式" ,
                'value' => php_sapi_name() ,
            ) ,
            array(
                'name' => "操作系统" ,
                'value' => PHP_OS,
            ) ,
            array(
                'name' => "服务器端信息" ,
                'value' => $_SERVER['SERVER_SOFTWARE'],
            ) ,
            array(
                'name' => "最大上传限制" ,
                'value' => get_cfg_var("upload_max_filesize") ? get_cfg_var("upload_max_filesize") : "不允许上传附件" ,
            ) ,
            array(
                'name' => "最大执行时间" ,
                'value' => get_cfg_var("max_execution_time") ? get_cfg_var("max_execution_time") . "秒" : "无限" ,
            ) ,
            array(
                'name' => "最大内存数" ,
                'value' => get_cfg_var("memory_limit") ? get_cfg_var("memory_limit") : "无" ,
            ) ,
            array(
                'name' => "Optimizer Version" ,
                'value' => defined("OPTIMIZER_VERSION") ? OPTIMIZER_VERSION : "无" ,
            ) ,
        );
    }
}
