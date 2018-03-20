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


class community_ctl_admin_user extends desktop_controller
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

        $this->finder('community_mdl_users', array(
            'title' => '用户列表',
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'use_buildin_recycle' => true,
            //'actions' => $actions,
            //'base_filter'=>array('for_blog_id'=>0)
        ));
    } //End Function
    public function edit($user_id)
    {
        $mdl_users = $this->app->model('users');
        $mdl_user_lv = $this->app->model('user_lv');
        $user_lv_schema = $mdl_user_lv->getList('user_lv_id,name');
        $this->pagedata['user_lv_arr'] = $user_lv_schema;
        $this->pagedata['user'] = $mdl_users->dump($user_id);
        $this->page('admin/user/edit.html');
    }
    public function save()
    {
        $this->begin();
        $mdl_users = $this->app->model('users');
        $is_save = $mdl_users->save($_POST);
        $this->end($is_save);
    }
    public function get_follow($user_id)
    {
        $mdl_relation = $this->app->model('relation');
        $this->pagedata['data_list'] = $mdl_relation->getRelationList($user_id);
        $this->display('admin/user/follow.html');
    }
} //End Class
