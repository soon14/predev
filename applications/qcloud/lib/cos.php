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


class qcloud_cos extends base_openapi
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function auth($p = false)
    {
        $user_obj = vmc::singleton('b2c_user_object');
        $member_id = $user_obj->get_member_id();
        if(!$member_id){
            $this->failure('未知member');
        }
        $params = vmc::singleton('base_component_request')->get_params(true);
        if(!is_object($p)){
            $p = array();
        }
        $params = array_merge($p,$params);
        $bucket = $params['bucket'];
        $path = $params['path'];
        if(strrpos($path,'/') == 0){
            $path = substr($path,1);
        }
        if (empty($bucket) || empty($path)) {
            $this->failure();
        }
        $expired_time = 0; //秒
        $current_time = time();
        $rand = substr(md5(time()), -10);
        $fileid = urlencode('/'.COS_APPID.'/'.$bucket.'/'.$path); // 唯一标识存储资源的
        $plain =
        'a='.COS_APPID.
        '&k='.COS_SECRETID.
        '&e='.$expired_time.
        '&t='.time().
        '&r='.$rand.
        '&f='.$fileid.
        '&b='.$bucket;
        $sign = base64_encode(hash_hmac('sha1',$plain,COS_SECRETKEY,true).$plain);
        $this->success($sign);
    }
}
