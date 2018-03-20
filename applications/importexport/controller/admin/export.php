<?php

class importexport_ctl_admin_export extends importexport_controller{

    /**
     *后台显示队列
     *
     */
    public function queue_export(){
        $params = array(
            'title'=>'导出任务队列',
            'use_buildin_recycle'=>true,
            'orderBy' => 'create_date desc',
            'base_filter'=>array('type'=>'export'),
        );
        $this->finder('importexport_mdl_task',$params);
    }

    /**
     * 导出选择页面
     *
     */
    public function export_view(){

        //导出方式
        $this->pagedata['check_policy'] = $this->check_policy();

        //导出
        $this->pagedata['params'] = serialize($_GET['_params']);
        $this->pagedata['filter'] = serialize($_POST);

        //支持导出类型
        $this->pagedata['export_type'] = $this->export_support_filetype();
        $this->display('admin/export/export.html');
    }

    /*
     * 创建导出队列
     * */
    public function create_export(){
        $filter = unserialize($_POST['filter']);
        $params = unserialize($_POST['params']);
        $_POST['key'] = $this->gen_key();
        $queue_params = array(
            'filter'=>$filter,
            'app_id' =>$params['app'],
            'model'=>$params['mdl'],
            'filetype' => $_POST['filetype'],
            'policy' => $this->queue_policy(),
            'key'=> $_POST['key'],
        );
        $this->begin();
        if(system_queue::instance()->publish('importexport_tasks_runexport', 'importexport_tasks_runexport', $queue_params) &&app::get('importexport')->model('task')->create_task('export',$_POST)){
            $this->end(true,'创建导出任务成功！');
        }else{
            $this->end(false,'创建导入任务失败！');
        }
        //测试，直接导出数据到存储服务器，不进行队列
        #print_r($queue_params);exit;
        #vmc::singleton('importexport_tasks_runexport')->exec($queue_params);
    }

    /**
     * 导出成功后下载
     */
    public function queue_download(){
        if (!$this->file_download($msg)){
            echo $msg;
            exit;
        }
    }
}
