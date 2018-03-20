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


class community_ctl_mobile_blog extends community_mcontroller
{
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        // $this->verify_member();
    }
    /**
     * $list_type  normal 发现,relation 关注,local 同城
     */
    public function index($page = 1,$list_type = 'normal')
    {
        $filter = $_POST['filter'];
        if (!$filter) {
            $filter = array();
        }
        if($list_type!='normal'){
            switch ($list_type) {
                case 'relation':
                    $this->verify_member();
                    $mdl_users = $this->app->model('users');
                    $user = $mdl_users->get_user_bymember($this->app->member_id);
                    $filter['_LT_RELATION'] = $user['user_id'];
                    break;
                case 'local':
                    $filter['_LT_LOCAL'] = explode('|',$_POST['lnandla']);
                    break;
            }
        }
        $filter['status|notin'] = array('shield');
        $filter['disabled'] = 'false';
        $filter['blog_type'] = 'topic';
        $order_type = $_POST['order_type'];
        $mdl_users = $this->app->model('users');
        $this->_blog_pagedata($page, $filter,$order_type);
        $this->page('mobile/default.html');
    }

    public function my($page = 1)
    {
        $this->verify_member();
        $mdl_users = $this->app->model('users');
        $user = $mdl_users->get_user_bymember($this->app->member_id);
        $this->pagedata['user_data'] = $user;
        $filter = array(
            'blog_type'=>'topic',
            'author' => $user['user_id'],
            'disabled'=>'false'
        );
        $this->_blog_pagedata($page, $filter);
        $this->page('mobile/default.html');
    }

    public function my_follow_map()
    {
        $this->verify_member();
        $mdl_users = $this->app->model('users');
        $user = $mdl_users->get_user_bymember($this->app->member_id);
        $filter = array(
            'author' => $user['user_id'],
            'follow_blog_id|than' => 0,
            'blog_type|notin' => array('topic'),
        );
        $mdl_blog = $this->app->model('blog');
        $follow_data = $mdl_blog->getList('*', $filter);
        $follow_data = utils::array_change_key($follow_data, 'follow_blog_id', true);
        $map = array();
        foreach ($follow_data as $blog_id => $data) {
            foreach ($data as $key => $value) {
                $map['_'.$blog_id][$value['blog_type']] = 1;
            }
        }
        $this->pagedata['my_follow_map'] = $map;
        $this->page('mobile/default.html');
    }

    public function get_follow($blog_id,$page)
    {
        $filter = $_POST['filter'];
        if (!$filter) {
            $filter = array();
        }
        $filter['follow_blog_id'] = $blog_id;
        $filter['status|notin'] = array('shield');
        $filter['disabled'] = 'false';
        $filter['blog_type'] = 'comment';
        $order_type = $_POST['order_type'];
        $mdl_users = $this->app->model('users');
        $this->_blog_pagedata($page, $filter,$order_type);
        $this->pagedata['blog_follow_data'] = $this->pagedata['blog_data'];
        unset($this->pagedata['blog_data']);
        $this->page('mobile/default.html');
    }


    public function user($user_id, $page = 1)
    {
        $mdl_users = $this->app->model('users');
        $user = $mdl_users->dump($user_id);
        $user = $mdl_users->get_user_bymember($user['member_id']);
        $this->pagedata['user_data'] = $user;
        $filter = array(
            'status|notin' => array('shield'),
            'blog_type'=>'topic',
            'disabled'=>'false',
            'author' => $user['user_id']
        );
        $this->_blog_pagedata($page, $filter);
        $this->page('mobile/default.html');
    }

    private function _blog_pagedata($page = 1, $filter = array(), $order_type)
    {
        $limit = 20;
        $mdl_blog = $this->app->model('blog');
        $blog_data = $mdl_blog->get_bloglist($filter, $page - 1, $limit, $order_type,$count);
        $this->pagedata['blog_data'] = $blog_data;
        $this->pagedata['count'] = $count;
        $this->pagedata['pager'] = array(
            'total' => ceil($count / $limit) ,
            'current' => $page,
            'link' => array(
                'app' => 'community',
                'ctl' => 'mobile_blog',
                'act' => 'index',
                'args' => array(
                    ($token = time()),
                    $user_id,
                ) ,
            ) ,
            'token' => $token,
        );
    }
}
