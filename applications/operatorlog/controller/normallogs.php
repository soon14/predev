<?php
/**
* 查看操作员日志控制器
*/
class operatorlog_ctl_normallogs extends desktop_controller
{
    /**
    * 操作员日志列表
    * @access public
    */
    public function index()
    {
        $this->finder(
            'operatorlog_mdl_normallogs',  array(
                'title' => $this->app->_('操作日志'),
                'allow_detail_popup'=>true,
                'use_buildin_filter' => true,
                'use_buildin_recycle' => false,
                'use_buildin_selectrow'=>false,

            )
        );
    }//End Function

}//End Class
