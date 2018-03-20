<?php

class importexport_ctl_admin_system extends importexport_controller{

    public function __construct($app)
    {
        parent::__construct($app);
    }

    /**
     * 显示导入导出存储配置参数文件页面
     */
    public function setting()
    {
        $storage = $this->storage_policy();
        $this->pagedata['storage'] = $storage;
        $this->pagedata['policy'] = $storage['policy']; //存储类型
        $this->pagedata['params'] = $storage['params']; //页面调用参数
        $this->pagedata[$storage['var_server_params']] = $this->get_storage_params();//配置参数
        $this->page($storage['view']['html'],$storage['view']['app']);
    }

    /**
     * 保存导入导出存储方式配置参数
     */
    public function save(){
        $this->begin();
        if( $this->set_storage_params($_POST) ){
            if(!vmc::singleton('importexport_policy')->check()){
                $this->end(false,'配置保存成功，但似乎FTP服务无法正常工作');
            }
            $this->end(true,'保存成功,FTP服务测试成功！');
        }else{
            $this->end(false,'保存失败');
        }
    }



}
