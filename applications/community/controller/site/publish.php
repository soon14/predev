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


class community_ctl_site_publish extends community_controller
{
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->app = $app;
        $this->_response->set_header('Cache-Control', 'no-store');
        vmc::singleton('base_session')->start();
    }
    public function index()
    {
        $this->title = '发布';
        $this->set_tmpl('communitypublish');
        $this->page('site/publish.html');
    }
    public function check_member()
    {
        if ($this->verify_member()) {
            $member_info = vmc::singleton('b2c_user_object')->get_member_info();
            $member_info['avatar_url'] = base_storager::image_path($member_info['avatar'], 'm');
            $this->splash('success', null, '已登录状态', true, $member_info);
        }
    }
    public function qrcode_login($session_str = false, $member_id)
    {
        if (!$session_str) {
            $session_str = utils::encrypt(array(
                'session_id' => vmc::singleton('base_session')->sess_id(),
                'time' => time(),
            ));
            $session_str = app::get('mobile')->router()->encode_args($session_str);
            ectools_qrcode_QRcode::png($session_str, false, 0, 5, 5);
        } else {
            if (!$member_id) {
                $this->splash('error', null, '未知member_id');
            }
            $member_info = vmc::singleton('b2c_user_object')->get_member_info($member_id);
            if (!$member_info) {
                $this->splash('error', null, '未知member_info');
            }
            $session_array = utils::decrypt($session_str);
            $session_id = $session_array['session_id'];
            $str_create_time = $session_array['time'];
            if ($str_create_time + 300 < time()) {
                $this->splash('error', null, 'session_str过期:'.$session_id);
            } else {
                vmc::singleton('base_session')->set_sess_id($session_id);
                vmc::singleton('b2c_user_object')->set_member_session($member_id);
                $this->splash('success', null, '登录成功');
            }
        }
    }

    public function save()
    {
        $this->verify_member();
        $this->begin();
        $member_id = $this->app->member_id;
        $mdl_users = $this->app->model('users');
        $user = $mdl_users->get_user_bymember($member_id);
        $user_id = $user['user_id'];
        if (!$user_id) {
            $this->end(false, '未知社区用户');
        }
        $media = json_decode($_POST['media'], true);
        $tag_setting = preg_replace("/(\n)|(\s)|(\t)|(\')|(')|(，)/" ,',' ,$_POST['blog']['blog_tag']);
        unset($_POST['blog']['blog_tag']);
        unset($_POST['media']);
        $params = utils::_filter_input($_POST);
        $blog = $params['blog'];
        $mdl_blog = $this->app->model('blog');
        $mdl_media_video = $this->app->model('media_video');
        if (!$blog['blog_id']) {
            $blog['blog_id'] = $mdl_blog->apply_id();
            $blog['createtime'] = time();
            $blog['author'] = $user_id;
            $blog['mode'] = 'video';
        }
        if ($tag_setting) {
            $blog['tag_setting'] = $tag_setting;
            $tags = array_unique(explode(',', $tag_setting));
            foreach ($tags as $key => $value) {
                if(empty($value)){
                    continue;
                }
                $blog['blog_tag'][] = array(
                    'blog_id'=>$blog['blog_id'],
                    'tag_name'=>$value
                );
            }
        }
        $media['video_id'] = md5($media['ident']);
        $media['target_id'] = $blog['blog_id'];

        if (!$mdl_media_video->save($media)) {
            $this->end(false, '保存video失败');
        }
        logger::debug($blog);
        if (!$mdl_blog->save($blog)) {
            $this->end(false, '保存基本信息失败');
        }

        $this->end(true, '完成发布');
    }
}
