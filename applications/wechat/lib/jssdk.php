<?php

class wechat_jssdk extends base_openapi
{
    public function __construct($app)
    {
        $this->stage = vmc::singleton('wechat_stage');
    }

    public function inject($params)
    {
        $eid = $params['eid'];
        $callback = $params['callback'];
        $token = $this->stage->get_token($eid);
        $url = $params['url']?urldecode($params['url']):$_SERVER['HTTP_REFERER'];
        $bind = app::get('wechat')->model('bind')->getRow('*', array('token' => $token));
        if (!$bind) {
            $this->failure_callback($callback, '未知公众号信息');
        }
        $access_token = $this->stage->get_access_token($bind['id']);
        if(!$access_token){
            $this->failure_callback($callback, '获得Accesstoken失败');
        }
        $ticket = $this->get_jsapiticket($access_token);
        if(!$ticket){
            $this->failure_callback($callback, 'jsapiticket 获得失败');
        }
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $noncestr = substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        $timestamp = time();
        $raw_string = "jsapi_ticket=$ticket&noncestr=$noncestr&timestamp=$timestamp&url=$url";
        $config = array(
            'appid' => $bind['appid'], // 必填，公众号的唯一标识
            'timestamp'=>$timestamp , // 必填，生成签名的时间戳
            'noncestr'=> $noncestr, // 必填，生成签名的随机串
            'signature'=> sha1($raw_string),// 必填，签名
            'rawstring'=>$raw_string
        );
        $this->success_callback($callback,$config);
    }

    private function get_jsapiticket($access_token)
    {

        $http_client = vmc::singleton('base_httpclient');
        if (!cacheobject::get('wechat_jsticket_'.$access_token, $ticket) || !$ticket) {
            $action =  "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$access_token";
            $respnese = $http_client->get($action);
            $respnese = json_decode($respnese, 1);
            $ticket = $respnese['ticket'];
            if ($ticket) {
                cacheobject::set('wechat_jsticket_'.$access_token, $ticket, time() + $respnese['expires_in']);
            }
        }
        return $ticket;
    }
}
