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


class serveradm_ctl_admin_xhprof extends desktop_controller
{


    public function index()
    {
        $this->finder('serveradm_mdl_xhprof',array(
            'title'=>"XHProf",
            'actions'=>array()
        ));
    }

    public function intro()
    {
        $this->page("/admin/intro.html");
    }

    public function doc()
    {
        $this->page("/admin/doc.html");
    }

    public function show($run_id){
        /*
        $oXHProf = $this->app->model('xhprof');
        $this->pagedata["data"] = $oXHProf->read_data($run_id);
        $this->display("/admin/show_data.html");*/
        echo "<iframe frameborder=0 scrolling=auto  src='".vmc::base_url(1)."/applications/serveradm/vendor/xhprof_html/index.php?run=".$run_id."&source=xhprof' width='100%' height='800'></iframe>";
    }
}
