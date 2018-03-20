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

class mobile_ctl_admin_setting extends desktop_controller
{

    public function index(){
        if($_POST){
            $this->begin();
            foreach ($_POST as $key => $value) {
                app::get('mobile')->setConf($key,$value);
            }
            $this->end(true,'保存成功');
        }

        include($this->app->app_dir.'/setting.php');
        foreach ($setting as $key => $value) {
            if($value['desc']){
                $this->pagedata['setting'][$key] = $value;
                $this->pagedata['setting'][$key]['value'] = $this->app->getConf($key);
            }
        }
        //vmc::dump($this->app->app_dir,$this->pagedata['setting']);
        $this->page('admin/setting.html');
    }


}//End Class
