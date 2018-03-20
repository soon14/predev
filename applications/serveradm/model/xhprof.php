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



/**
 * xhprof
 * serveradm_mdl_xhprof
 */
class serveradm_mdl_xhprof extends dbeav_model{
    var $defaultOrder = array('addtime','DESC');

    public function __construct($app) {
        parent::__construct($app);
        $this->dir = $this->dir_name();
    }

	public function delete($data,$subSdf = 'delete') {
		$this->pre_delete($data);
		return parent::delete($data);
	}

    public function pre_delete($data) {
        // 用来删除文件
        $this->delete_data($data["run_id"]);
        return true;
    }

    public function read_data($run_id)
    {
        $file_name = $this->file_name($run_id);
        if (!file_exists($file_name)) return false;

        $contents = file_get_contents($file_name);
        return unserialize($contents);
    }

    public function dir_name()
	{
		$path = ini_get("xhprof.output_dir");
		$path = empty($path)? sys_get_temp_dir() : $path;
		return $path;
        // return DATA_DIR."/xhprof/";
    }

    public function file_name($filename){
        return $this->dir . "/" . $filename . ".xhprof";
    }

    public function delete_data($run_id)
    {

			$file_name = $this->file_name($run_id);
			if (!file_exists($file_name)) return false;
			@unlink($file_name);
		
    }

    public function write_data($xhprof_data,$run_id = null)
    {
        $this->makedir();
        $id = $this->gen_id();

        $xhprof_data = serialize($xhprof_data);

        if ($run_id === null) {
            $run_id = $this->gen_id();
        }

        $file_name = $this->file_name($run_id);
        $file = fopen($file_name, 'w');

        if ($file) {
            fwrite($file, $xhprof_data);
            fclose($file);
            return $run_id;
        }
        return false;
    }

    private function gen_id()
    {
        return md5(vmc::request()->get_request_uri());
    }

    private  function makedir()
    {
        $dir = $this->dir;
        if(!is_dir($dir)) return mkdir($dir, 0777);
        return true;
    }
}
