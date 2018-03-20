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


 

class site_utility_download 
{

    public $max_runtime = 120;       //下载时长
    public $timeout = 120;           //超时长
    public $expire_time = 86400;
    private $work_dir = null;

    function __construct() 
    {
        $this->work_dir = DATA_DIR . '/tmp/theme';
    }//End Function

    public function get_work_dir() 
    {
        return $this->work_dir;
    }//End Function

    /*
     * 设置一个下载任务
     * @param array $task_params
     * @return boolean
     */
    public function set_task($task_params) 
    {
        if(empty($task_params)) return false;
        $this->clear_unused_folder($this->expire_time);   //仅限tmp目录
        $ident = date("Ymd").substr(md5(time().rand(0,9999)),0,5);
        $task_temp_dir = $this->work_dir . '/' . $ident;
        if(!is_dir($task_temp_dir)){
            mkdir($task_temp_dir, 0775, true);
        }
		/*
        $task_file = $task_temp_dir . "/task.php";
        if(file_put_contents($task_file, serialize($task_params))){
            return $ident;
        }
		*/
		if(app::get('site')->setConf($ident,serialize($task_params))){
			return $ident;
		}
        return false;
    }//End Function

    /*
     * 读取下载任务
     * @param string $ident
     * @return mixed
     */
    public function get_task($ident)
    {
		/*
        $task_file = $this->work_dir . '/' . $ident . '/task.php';
        if(is_file($task_file)){
            return unserialize(file_get_contents($task_file));
        }*/
		$params = app::get('site')->getConf($ident);
		if($params){
			return unserialize($params);
        }
        return false;         
    }//End Function

    /*
     * 下载任务
     * @param string $ident
     * @return boolean
     */
    public function dl($ident) 
    {
        $task_info = $this->get_task($ident);
        if(empty($task_info['url']))   return false;
       
        $download_file = $this->work_dir . '/' . $ident . '/' . $task_info['name'];
		if(!is_dir($this->work_dir . '/' . $ident)){
			mkdir($this->work_dir . '/' . $ident,0755,true);
		}
        touch($download_file);
        $this->file_res = fopen($download_file, 'rb+') or exit('Error: 无法创建文件:'.$download_file);
        fseek($this->file_res, 0, SEEK_END);

        $cur_size = ftell($this->file_res);
        $header = $cur_size ? array('Range'=>'bytes='.$cur_size.'-') : null;
        set_time_limit($this->max_runtime + 10);
        $this->start_time = time();
        ob_start();
        $netObj = vmc::singleton('base_httpclient');
        $netObj->timeout = $this->timeout;
        $netObj->get($task_info['url'], $header, array($this, 'dl_handle'));
        return ($this->success) ? $ident : false;
     }//End Function

    /*
     * 下载回调
     * @param object $ident
     * @param string $content
     * @return boolean
     */
     public function dl_handle($netcore, $content) 
     {
        $this->success = false;
        if($netcore->responseCode{0}==2){
            fputs($this->file_res, $content);
            if(time() - $this->start_time > $this->max_runtime){
                ob_end_clean();
                return false;
            }
            $this->success = true;
            return true;
        }
        ob_end_clean();
        return false;
     }//End Function

    /*
     * 清除无用任务目录
     * @param int $expire_time
     * @return
     */
    private function clear_unused_folder($expire_time=86400){
        if(is_dir($this->work_dir) && ($handle = opendir($this->work_dir))){
            while (false !==($file = readdir($handle))){
                $file_name=substr($file,0,8);
                if(is_int($file_name) && strlen($file_name)==8){
                    if((strtotime($file_name)+$expire_time)<time()){
                        remove_floder($path.'/'.$file);
                    }
                }
            }
        }
    }//End Function

    /*
     * 删除目录
     * @param string $path
     * @return boolean
     */
    private function remove_floder($path){
        if(($handle = opendir($path))){
            while (false !==($file = readdir($handle))){
                if($file!='.' && $file!='..'){
                    if(is_dir($file)){
                        remove_floder($path.'/'.$file);
                    }else{
                        @unlink($path.'/'.$file);
                    }
                }
            }
            closedir($handle);
            @rmdir($path);
        }
        return true;
    }
}//End Class
