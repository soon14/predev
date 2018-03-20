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


class community_ctl_mobile_setting extends community_mcontroller
{
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->verify_member();
    }

    /**
     * 编辑个人签名
     */
    public function user_sign()
    {
        $this->begin();
        $member_id = $this->app->member_id;
        $mdl_users = $this->app->model('users');
        $user = $mdl_users->get_user_bymember($member_id);
        $user_data = $mdl_users->dump($user['user_id']);
        if (!$user_data) {
            $this->end(false, '未知社区用户');
        }
        $params = utils::_filter_input($_POST);
        $user_data['sign'] = $params['sign'];
        if($mdl_users->save($user_data)){
            $this->end(true, '保存签名成功');
        }else{
            $this->end(false, '保存签名失败');
        }


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
