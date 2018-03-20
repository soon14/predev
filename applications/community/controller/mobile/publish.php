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


class community_ctl_mobile_publish extends community_mcontroller
{
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->verify_member();
    }
    /**
     * 主题发布.
     */
    public function topic()
    {
        $this->begin();
        $user = $this->_get_user();
        if (!$user) {
            $this->end(false, '未知社区用户');
        }
        $user_id = $user['user_id'];
        $tag_setting = preg_replace("/(\n)|(\s)|(\t)|(\')|(')|(，)/", ',', $_POST['blog']['blog_tag']);
        unset($_POST['blog']['blog_tag']);
        $params = utils::_filter_input($_POST);
        if(!empty($params['blog']['lnandla'])){
            $lnandla_arr = explode('|',$params['blog']['lnandla']);
            $params['blog']['longitude'] = $lnandla_arr[0];
            $params['blog']['latitude'] = $lnandla_arr[1];
        }

        $blog = $params['blog'];
        $media = $params['media'];
        $mdl_blog = $this->app->model('blog');
        if (!$blog['blog_id']) {
            $blog['blog_id'] = $mdl_blog->apply_id();
            $blog['createtime'] = time();
            $blog['author'] = $user_id;
        }
        if ($tag_setting) {
            $blog['tag_setting'] = $tag_setting;
            $tags = array_unique(explode(',', $tag_setting));
            foreach ($tags as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                $blog['blog_tag'][] = array(
                    'blog_id' => $blog['blog_id'],
                    'tag_name' => $value,
                );
            }
        }

        switch ($blog['mode']) {
            case 'video':
            case 'short_video':
                $media_model = 'media_video';
                $media['video_id'] = md5($media['ident']);
                $media['target_id'] = $blog['blog_id'];
                break;
            case 'audio':
                $media_model = 'media_audio';
                $media['audio_id'] = md5($media['ident']);
                $media['target_id'] = $blog['blog_id'];
                break;
            case 'image':
                $media_model = 'media_image_attach';
                break;
        }
        if ($media_model) {
            $mdl_media = $this->app->model($media_model);
            if ($media_model == 'media_image_attach') {
                if (empty($media['image_ids'])) {
                    $this->end(false, '保存meidia失败,无image_id');
                } else {
                    $image_ids = explode('|', $media['image_ids']);
                }
                foreach ($image_ids as $value) {
                    $image_attach = array(
                        'target_id' => $blog['blog_id'],
                        'image_id' => $value,
                    );
                    if (!$mdl_media->save($image_attach)) {
                        $this->end(false, '保存meidia失败');
                    }
                }
            } else {
                if (!$mdl_media->save($media)) {
                    $this->end(false, '保存meidia失败');
                }
            }
        }
        logger::debug($blog);
        if (!$mdl_blog->save($blog)) {
            $this->end(false, '保存基本信息失败');
        }

        $this->end(true, '完成发布');
    }
     /**
      * 点赞.
      */
     public function zan($blog_id, $unzan = false)
     {
         $this->begin();
         if (!$this->_check_blog($blog_id)) {
             $this->end(false, '参数错误:未知blog_id');
         }
         $mdl_blog = $this->app->model('blog');
         //$params = utils::_filter_input($_POST);
         $user = $this->_get_user();
         if (!$user) {
             $this->end(false, '未知用户');
         }
         $user_id = $user['user_id'];
         $exit_zan_filter = array(
             'blog_type' => $unzan ? 'unzan' : 'zan',
             'follow_blog_id' => $blog_id,
             'author' => $user_id,
         );
         if ($mdl_blog->count($exit_zan_filter)) {
             $this->end(false, '重复操作');
         }
         $new_zan_blog_id = $mdl_blog->apply_id();
         $new_zan = array(
             'blog_id' => $new_zan_blog_id,
             'blog_type' => $unzan ? 'unzan' : 'zan',
             'follow_blog_id' => $blog_id,
             'author' => $user_id,
             'createtime' => time(),
         );
         if (!$mdl_blog->save($new_zan)) {
             $this->end(false, '操作失败');
         } else {
             if (!$mdl_blog->update_blog_count($blog_id, $unzan ? 'unzan_count' : 'zan_count', 1)) {
                 $this->end(false, '操作失败');
             }
         }
         $this->end(true, '操作成功');
     }
      /**
       * 不赞.
       */
      public function unzan($blog_id)
      {
          $this->zan($blog_id, 'reverse');
      }
      /**
       * 转发.
       */
      public function share($blog_id)
      {
          $this->begin();
          if (!$this->_check_blog($blog_id)) {
              $this->end(false, '参数错误:未知blog_id');
          }
          $mdl_blog = $this->app->model('blog');
        //   $user = $this->_get_user();
        //   if (!$user) {
        //       $this->end(false, '未知用户');
        //   }
        //   $user_id = $user['user_id'];
          if (!$mdl_blog->update_blog_count($blog_id, 'share_count', 1)) {
              $this->end(false, '操作失败');
          }
          $this->end(true, '操作成功');
      }
       /**
        * 评论.
        */
       public function comment($blog_id)
       {
           $this->begin();
           if (!$this->_check_blog($blog_id)) {
               $this->end(false, '参数错误:未知blog_id');
           }
           $mdl_blog = $this->app->model('blog');
           $params = utils::_filter_input($_POST);
           $user = $this->_get_user();
           if (!$user) {
               $this->end(false, '未知用户');
           }
           $user_id = $user['user_id'];
           $new_comment_blog_id = $mdl_blog->apply_id();
           $new_comment = array(
               'blog_id' => $new_comment_blog_id,
               'blog_type' => 'comment',
               'follow_blog_id' => $blog_id,
               'content'=>$params['content'],
               'author' => $user_id,
               'createtime' => time(),
           );
           if (!$mdl_blog->save($new_comment)) {
               $this->end(false, '操作失败');
           } else {
               if (!$mdl_blog->update_blog_count($blog_id,'comment_count', 1)) {
                   $this->end(false, '操作失败');
               }
           }
           $this->end(true, '操作成功');
       }
       /**
        * 删除,恢复  ,disabled
        */
       public function disabled($blog_id,$dis_val = 'true'){
           $this->begin();
           $mdl_blog = $this->app->model('blog');
           $params = utils::_filter_input($_POST);
           $user = $this->_get_user();
           if (!$user) {
               $this->end(false, '未知用户');
           }
           $user_id = $user['user_id'];
           $is_disabled = $mdl_blog->update(array('disabled'=>$dis_val),array('author'=>$user_id,'blog_id'=>$blog_id));
           if (!$is_disabled) {
               $this->end(false, '操作失败');
           }
           $this->end(true, '操作成功');
       }
       /**
        * 检测blog_id 有效性.
        */
       private function _check_blog($blog_id)
       {
           $mdl_blog = $this->app->model('blog');
           $filter = array(
                            'disabled' => 'false',
                            'status|notin' => array('shield'),
                            'blog_id' => $blog_id,
                        );

           return $blog_id && $mdl_blog->count($filter);
       }
       /**
        * 获得当前用户数据.
        */
       private function _get_user()
       {
           $member_id = $this->app->member_id;
           $mdl_users = $this->app->model('users');
           $user = $mdl_users->get_user_bymember($member_id);
           $user_id = $user['user_id'];
           if (!$user_id) {
               return false;
           }

           return $user;
       }
    /**
     * 图片上传.
     */
    public function image_upload()
    {
        $image = app::get('image')->model('image');
        $image_name = $_FILES['file']['name'];
        $image_id = $image->store($_FILES['file']['tmp_name'], null, null, $image_name);
        if (!$return_size) {
            header('Content-Type:application/json; charset=utf-8');
            echo json_encode(array('url' => base_storager::image_path($image_id), 'image_id' => $image_id));
        } else {
            header('Content-Type:application/json; charset=utf-8');
            echo json_encode(array('url' => base_storager::image_path($image_id, $return_size), 'image_id' => $image_id));
        }

        return $image_id;
    }
}
