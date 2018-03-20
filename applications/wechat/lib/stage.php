<?php

class wechat_stage
{
    public function __construct($app)
    {
    }
    public function gen_eid()
    {
        do {
            $eid = $this->randomkeys(6);
            $row = app::get('wechat')->model('bind')->count(array('eid' => $eid));
        } while ($row);

        return $eid;
    }
    public function get_api($eid)
    {
        return $url = vmc::openapi_url('openapi.wechat', 'api', array('eid' => $eid));
    }
    public function gen_token()
    {
        return md5('wechat'.$this->randomkeys(12));
    }
    public function get_token($eid)
    {
        $bind = app::get('wechat')->model('bind')->getRow('token', array('eid' => $eid));
        return $bind['token'];
    }

    public function get_access_token($bind_id = false,$app_define = false)
    {


        if($app_define){
            $app_id = $app_define['app_id'];
            $app_secret = $app_define['app_secret'];
        }else{
            $bind = app::get('wechat')->model('bind')->dump($bind_id);
            $app_id = $bind['appid'];
            $app_secret = $bind['appsecret'];
        }

        $http_client = vmc::singleton('base_httpclient');
        if (!cacheobject::get('wechat_access_token_'.$app_id, $access_token) || !$access_token) {
            $access_token_action = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$app_id&secret=$app_secret";
            $respnese = $http_client->get($access_token_action);
            $respnese = json_decode($respnese, 1);
            $access_token = $respnese['access_token'];
            if ($access_token) {
                cacheobject::set('wechat_access_token_'.$app_id, $access_token, time() + $respnese['expires_in']);
            }
        }

        return $access_token;
    }

    /**
     * 微信短连接
     */
     public function gen_surl($action_url,$access_token,&$msg){
         $http_client = vmc::singleton('base_httpclient');

         $res = $http_client->post("https://api.weixin.qq.com/cgi-bin/shorturl?access_token=$access_token",json_encode(array(
             //'access_token'=>$access_token,
             'action'=>'long2short',
             'long_url'=>$action_url
         )));
         $res = json_decode($res,1);
         if($res['errcode'] === 0){
             return $res['short_url'];
         }
         $msg = $res['errmsg'];
         return false;
     }
    /**
     * 验证消息签名.
     */
    public function check_sign($sign, $timestamp, $nonce, $token)
    {
        $tmp_arr = array(
            $token,
            $timestamp,
            $nonce,
        );
        sort($tmp_arr, SORT_STRING);
        $tmp_str = sha1(implode($tmp_arr));

        return (strtoupper($tmp_str) == strtoupper($sign));
    }
    /**
     * 事件回复.
     * //TODO  目前只支持关注、取消关注事件.
     */
    public function event_reply($post_data)
    {
        $mdl_bind = app::get('wechat')->model('bind');
        $mdl_replyrule = app::get('wechat')->model('replyrule');
        $mdl_media = app::get('wechat')->model('media');
        $bind = $mdl_bind->getRow('*', array(
            'wechat_id' => $post_data['ToUserName'],
            'status' => 'active',
        ));
        if (empty($bind)) {
            return;
        }
        $rule = $mdl_replyrule->getRow('*', array('reply_type' => $post_data['Event']));
        if (empty($rule)) {
            return;
        }
        $media = $mdl_media->dump($rule['replyrule_id']);
        if ($bind['id'] != $media['bind_id']) {
            return;
        }
        $echo_data = array(
            'ToUserName' => $post_data['FromUserName'],
            'FromUserName' => $bind['wechat_id'],
            'MsgType' => $media['wtype'],
        );
        $this->pre_echo_data($echo_data, $media);
        $this->echo_msg($echo_data, $bind);
    }
     /**
      * 普通消息回复.
      */
     public function normal_reply($post_data)
     {
         $mdl_bind = app::get('wechat')->model('bind');
         $mdl_replyrule = app::get('wechat')->model('replyrule');
         $mdl_media = app::get('wechat')->model('media');
         $bind = $mdl_bind->getRow('*', array(
             'wechat_id' => $post_data['ToUserName'],
             'status' => 'active',
         ));
         if (empty($bind)) {
             return;
         }
         switch ($post_data['MsgType']) {
             case 'text':
                 $msg = $post_data['Content'];
                 $rule = $mdl_replyrule->getRow('*', array('keywords|has' => $msg.','));
         if (empty($rule)) {
             return;
         }
                 $media = $mdl_media->dump($rule['replyrule_id']);
        if ($bind['id'] != $media['bind_id']) {
            return;
        }
                 $echo_data = array(
                     'ToUserName' => $post_data['FromUserName'],
                     'FromUserName' => $bind['wechat_id'],
                     'MsgType' => $media['wtype'],
                 );

         $this->pre_echo_data($echo_data, $media);
         $this->echo_msg($echo_data, $bind);
                 break;
             default:
                 # code...
                 break;
         }
         //TODO
     }

    /**
     * 发送消息.
     */
    public function echo_msg($data, $bind)
    {
        $data['CreateTime'] = time();
        $post_xml = vmc::singleton('mobile_utility_xml')->array2xml($data, 'xml');
        $obj_crypt = new wechat_crypt($bind['token'], $bind['aeskey'], $bind['appid']);
        $flag = $obj_crypt->encryptMsg($post_xml, $data['CreateTime'], $this->randomkeys(5), $encode_post_xml);
        if ($flag === 0) {
            echo $encode_post_xml;
        } else {
            logger::error('回应消息时，加密失败!ERROR_CODE:'.$flag.$post_xml.var_export($data, 1).var_export($bind, 1));

            return;
        }
    }

    //随机取6位字符数
    private function randomkeys($length)
    {
        $key = '';
        $pattern = '1234567890';    //字符池
        for ($i = 0;$i < $length;$i++) {
            $key .= $pattern{mt_rand(0, 9)};    //生成php随机数
        }

        return $key;
    }
    /**
     * 包装数据.
     */
    private function pre_echo_data(&$echo_data, $media)
    {
        switch ($echo_data['MsgType']) {
            case 'news':
               $echo_data['ArticleCount'] = count($media['wcontent']['Articles']);
               $echo_data['Articles'] = $media['wcontent']['Articles'];
                break;
            case 'image':
               $echo_data['Image']['MediaId'] = $media['wcontent']['MediaId'];
                break;
            case 'video':
               $echo_data['Video']['MediaId'] = $media['wcontent']['MediaId'];
               $echo_data['Video']['Title'] = $media['wcontent']['Title'];
               $echo_data['Video']['Description'] = $media['wcontent']['Description'];
                break;
            case 'music':
                   $echo_data['Music']['MusicUrl'] = $media['wcontent']['MusicUrl'];
                   $echo_data['Music']['Title'] = $media['wcontent']['Title'];
                   $echo_data['Music']['Description'] = $media['wcontent']['Description'];
                   $echo_data['Music']['ThumbMediaId'] = $media['wcontent']['ThumbMediaId'];
                   $echo_data['Music']['HQMusicUrl'] = $media['wcontent']['HQMusicUrl'];
               break;
            case 'voice':
               $echo_data['Voice']['MediaId'] = $media['wcontent']['MediaId'];
               break;
            default: // 文字
               $echo_data['Content'] = $media['wcontent']['text'];
               break;
        }
    }
}
