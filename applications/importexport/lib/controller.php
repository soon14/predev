<?php

class importexport_controller extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

    /**
     * 导出所支持的格式.
     *
     * @return array
     */
    public function export_support_filetype()
    {
        $filetype = array(
            'csv' => '.csv',
            'xls' => '.xls',
        );

        return $filetype;
    }

    /**
     * 导入支持的格式.
     *
     * @return array
     */
    public function import_support_filetype()
    {
        $filetype = array(
            'csv' => '.csv',
            'xls' => '.xls',
        );

        return $filetype;
    }

    /**
     * 提供导入导出的存储方式.
     *
     * @return array $storage 返回存储方式的参数
     */
    public function storage_policy()
    {
        return vmc::singleton('importexport_policy')->storage_policy();
    }

    /**
     * 保存存储方式的配置参数.
     *
     * @params array $params
     *
     * @return bool
     */
    public function set_storage_params($params)
    {
        return vmc::singleton('importexport_policy')->set_storage_params($params);
    }

    /**
     * 获取存储方式的配置参数.
     *
     * @return array $params
     */
    public function get_storage_params()
    {
        return vmc::singleton('importexport_policy')->get_storage_params();
    }

    /**
     * 检查导出,导入时，是否开启文件存储方式.
     *
     * @return bool
     */
    public function check_policy()
    {
        if (!$this->get_storage_params()) {
            return false;
        }

        return true;
    }

    /**
     * 队列导出,导入，提供文件存储方式(默认提供ftp服务存储).
     */
    public function queue_policy()
    {
        $server = $this->storage_policy();

        return  $server['policy'];
    }

    /**
     * 导出队列唯一key,并且用于生成远程文件名称.
     */
    public function gen_key($type = 'export')
    {
        $key = $type.'_'.md5(cachemgr::ask_cache_check_version().time());
        return $key;
    }

    /**
     * 后台条件过滤和解析.
     */
    public function view_filter($filter, $params)
    {
        $ctl_class = $filter['app'].'_ctl_'.$filter['ctl'];
        $mdl_class = $params['app'].'_mdl_'.$params['mdl'];
        $_POST['view'] = $filter['view'];
        $view_filter = $this->get_view_filter($ctl_class, $mdl_class);
        $filter = array_merge($filter, $view_filter);

        return $filter;
    }

    public function import_message($status = false, $msg)
    {
        header('content-type:text/html; charset=utf-8');
        if ($status) {
            $status_msg = '上传成功';
            echo "<script>parent.Messagebox.success('$msg','$status_msg')</script>";
        } else {
            $status_msg = '上传失败';
            echo "<script>parent.Messagebox.error('$msg','$status_msg')</script>";
        }
        exit;
    }

     /**
      * 导出文件下载
      */
    public function file_download(&$msg)
    {
        $params = app::get('importexport')->model('task')->getList('*', array('task_id' => $_GET['task_id']));
        $params = $params[0];

        if ($params['status'] == '2' || $params['status'] == '6' || $params['status'] == '8') {
            //连接存储服务器
            $this->policyObj = vmc::singleton('importexport_policy');
            $msg = $this->policyObj->connect();
            if ($msg !== true) {
                return false;
            }

            $remote_file_name = $params['key'];
            $local_file_name = $params['key'].'.'.$params['filetype'];
            if (!$this->policyObj->local_io()) {
                $msg = '本地文件创建失败，请检查'.TMP_DIR.'文件夹权限';

                return false;
            }

            $filetypeObj = vmc::singleton('importexport_type_'.$params['filetype']);
            $resumepos = $filetypeObj->set_queue_header($local_file_name, $this->policyObj->size($remote_file_name));
            if (!$this->policyObj->pull($this->policyObj->local_file, $remote_file_name, $resumepos, $msg)) {
                return false;
            }

            //实例化导出文件类型类
            $file = fopen($this->policyObj->local_file, 'rb');
            if (method_exists($filetypeObj, 'setBom')) {
                $bom = $filetypeObj->setBom();
                echo $bom;
            }
            while (!feof($file)) {
                set_time_limit(0);
                print_r(fread($file, 1024 * 8));
                ob_flush();
                flush();
            }
            $this->policyObj->local_clean();
        }
        exit;
    }
}
