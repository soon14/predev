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


class wechat_mdl_media extends dbeav_model
{
    public function getRemoteList($access_token, $type = 'news', $page = 1, &$count)
    {
        //$media_count_action="https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token=$access_token";
        //$media_count = $http_client->get($media_count_action);
        //$media_count = json_decode($media_count,1);
        //$count = $media_count[$type.'_count'];

        $media_action = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=$access_token";
        $http_client = vmc::singleton('base_httpclient');
        $media_res = $http_client->post($media_action, json_encode(array(
            'type' => $type,
            'offset' => ($page - 1) * 20,
            'count' => 20,
        )));
        $media_res = json_decode($media_res, 1);
        $count = $media_res['total_count'];

        return $media_res['item'];
    }

    public function uploadimg($access_token, $image_id, &$msg)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=$access_token";
        $image_path  =base_storager::image_path($image_id);
        if(substr($image_path,0,2) == '//'){
            $image_path = 'http:'.$image_path;
        }elseif(substr($image_path,0,4)!='http'){
            $image_path = vmc::base_url().'/'.$image_path;
        }
        $image_content = file_get_contents($image_path);
        $image_info = getimagesize($image_path);
        $mime = explode('/',$image_info['mime']);
        $tmp_target = tempnam(TMP_DIR, 'img').'.'.$mime[1];
        file_put_contents($tmp_target, $image_content);
        $ch1 = curl_init();
        $timeout = 5;
        $data = array('media' => "@{$tmp_target}");
        curl_setopt($ch1, CURLOPT_URL, $url);
        curl_setopt($ch1, CURLOPT_POST, true);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch1, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch1, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch1);
        curl_close($ch1);
        @unlink($tmp_target);
        $result = json_decode($result, true);
        if ($result && $result['url']) {
            return $result['url'];
        } else {
            $msg = '图片同步到微信失败!'.$result['errmsg'];
            return false;
        }
    }
}
