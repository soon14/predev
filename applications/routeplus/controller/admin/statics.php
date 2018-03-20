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

class routeplus_ctl_admin_statics extends desktop_controller
{
    /**
    * 操作员日志列表
    * @access public
    */
    public function index()
    {
        $this->finder(
            'routeplus_mdl_statics', array(
                'title' => '自定义URL列表',
                'use_buildin_recycle'=>true,
                'actions'=>array(
                    array(
                        'label' => ('添加自定义URL') ,
                        'icon' => 'fa-plus',
                        'href' => 'index.php?app=routeplus&ctl=admin_statics&act=edit',
                    )
                )
            )
        );
    }//End Function

    public function edit($id){
        $mdl_rstatics = $this->app->model('statics');
        $this->pagedata['base_url'] = vmc::base_url(true);
        $this->pagedata['app_url_map'] = vmc::$app_url_map;

        if($_POST){
            $save_data = $_POST;
            if($save_data['id']){
                $this->begin();
            }else{
                $this->begin('index.php?app=routeplus&ctl=admin_statics&act=index');
            }

            $this->end($mdl_rstatics->save($save_data));
        }else{
            $this->display('admin/edit_statics.html');
        }

    }

}//End Class
