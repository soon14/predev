<?php
/**
 *
 * 维权信息查看
 */
class wechat_ctl_admin_business_alert extends desktop_controller{



    /*
     * @param object $app
     */
    function __construct($app)
    {
        parent::__construct($app);
    }//End Function

    //关注自动回复信息设置
    public function index(){
        $this->finder(
            'wechat_mdl_alert',
            array(
                'title'=>'告警通知',
                'use_buildin_recycle'=>true,
            )
        );
    }



}
