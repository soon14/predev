<?php
/**
* 查看操作员日志控制器
*/
class operatorlog_ctl_default extends desktop_controller
{
    /**
    * 操作员日志列表
    * @access public
    */
    public function index()
    {
        $this->finder(
            'operatorlog_mdl_logs', array(
                'title' => $this->app->_('操作日志'),
                'use_buildin_selectrow'=>false,
                'use_buildin_filter' => true,
            )
        );
    }//End Function

}//End Class
