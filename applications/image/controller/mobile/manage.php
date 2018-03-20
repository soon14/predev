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



class image_ctl_mobile_manage extends mobile_controller
{
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
    }
    //小程序上传图片
    function wx_upload($rebuild=false,$return_size = false){
        $member_id = vmc::singleton('b2c_user_object')->get_member_id();
        if (!$member_id) {
            $this->splash('error',null,'未知用户状态');
        }
        $image = $this->app->model('image');
        $image_name = $_FILES['file']['name'];
        $image_id = $image->store($_FILES['file']['tmp_name'],null,null,$image_name);
        if($_POST['tag']){
            $this->_set_tag($image_id,array($_POST['tag']));
        }
        if($rebuild){
            $image->rebuild($image_id,array('L','M','S','XS'));
        }
        if(!$return_size){
            header('Content-Type:application/json; charset=utf-8');
            echo json_encode(array('url'=>base_storager::image_path($image_id),'image_id'=>$image_id));
        }else{
            header('Content-Type:application/json; charset=utf-8');
            echo json_encode(array('url'=>base_storager::image_path($image_id,$return_size),'image_id'=>$image_id));
        }

        return $image_id;
    }

    /**
     * 设置图片的tag-本类私有方法
     * @param null
     * @return null
     */
    private function _set_tag($image_id,$tag_name){
        $tagctl   = app::get('desktop')->model('tag');
        $tag_rel   = app::get('desktop')->model('tag_rel');
        $data['rel_id'] = $image_id;
        $tags = is_array($tag_name)?$tag_name:explode(' ',$_POST['tag']['name']);
        $data['tag_type'] = 'image';
        $data['app_id'] = 'image';
        foreach($tags as $key=>$tag){
            if(!$tag) continue;
            $data['tag_name'] = $tag; //todo 避免重复标签新建
            $tagctl->save($data);
            if($data['tag_id']){
                $data2['tag']['tag_id'] = $data['tag_id'];
                $data2['rel_id'] = $image_id;
                $data2['tag_type'] = 'image';
                $data2['app_id'] = 'image';
                $tag_rel->save($data2);
                unset($data['tag_id']);
            }
        }
    }

}
