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


class community_ctl_admin_blog extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
    }
    public function index()
    {
        // $actions = array(
        //     array(
        //         'label' => '' ,
        //         'icon' => '',
        //         'href' => '',
        //     ) ,
        // );

        $this->finder('community_mdl_blog', array(
            'title' => '主题列表',
            'use_buildin_set_tag' => $use_buildin_set_tag,
            'use_buildin_filter' => true,
            'use_buildin_recycle' => $use_buildin_recycle,
            //'actions' => $actions,
            'base_filter'=>array('blog_type'=>'topic')
        ));
    } //End Function

    public function edit($blog_id){
        $mdl_blog = $this->app->model('blog');
        $blog = $mdl_blog->dump($blog_id);
        $this->pagedata['blog'] = $blog;
        $this->page('admin/blog/edit.html');
    }

    public function save(){
        $this->begin();
        $mdl_blog = $this->app->model('blog');
        $is_save = $mdl_blog->save($_POST);
        $this->end($is_save);
    }

} //End Class
