<?php
/**
 * 微信免登陆配置
 */
class wechat_ctl_admin_sso extends desktop_controller{

    /*
     * @param object $app
     */
    function __construct($app)
    {
        parent::__construct($app);
    }//End Function

    public function index(){


        $this->page('admin/sso.html');
    }

    public function save_setting(){
        $this->begin();
        $this->end(false,'FALSE');

    }
}
