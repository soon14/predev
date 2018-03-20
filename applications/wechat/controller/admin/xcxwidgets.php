<?php

/**
 * 微信小程序挂件.
 */
class wechat_ctl_admin_xcxwidgets extends desktop_controller
{
    /*
     * @param object $app
     */
    public function __construct($app)
    {
        parent::__construct($app);
    }//End Function

    public function index()
    {
        $mdl_xcxwidgets = $this->app->model('xcxwidgets');
        $this->pagedata['list'] = $mdl_xcxwidgets->getList();
        $this->display('admin/xcxwidgets/center.html');
    }

    public function html($name,$type='setting'){
        $mdl_xcxwidgets = $this->app->model('xcxwidgets');
        //$widget_data = utils::filter_input($_POST);
        $widget_data = $_POST;
        $widget_info = $mdl_xcxwidgets->dump($name,$widget_data);
        echo $widget_info[$type.'_html'];
        // $setting_html = $widget_info['setting_html'];
        // $preview_html = $widget_info['preview_html'];
    }
    
    public function gets() {
        $mdl_xcxwidgets = $this->app->model('xcxwidgets');
        $xcxwidgets = $mdl_xcxwidgets->getList();
        echo json_encode($xcxwidgets);
        return;
    }


}
