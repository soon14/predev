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


class vmcocean_openapi_anlytics extends base_openapi
{
    public function tracker($params)
    {
        header('Content-type: image/png');
        $encrypt_str = ($params['track']);
        $encrypt_str = vmc::singleton('site_router')->decode_args($encrypt_str);
        $track_arr = utils::decrypt($encrypt_str);
        if (!is_array($track_arr) || empty($track_arr['event_name'])) {
            return;
        }
        $vmc_uid = $_COOKIE['_VMC_UID'];
        $member_ident = $_COOKIE['MEMBER_IDENT'];
        $uid = $member_ident ? $member_ident : $vmc_uid;
        if (!$uid) {
            return;
        }
        $event_name = $track_arr['event_name'];
        $track_params = array(
            '$time' => (int) (microtime(true) * 1000),
            '$ip' => base_request::get_remote_addr(),
            'HTTP_REFERER' => $_SERVER['HTTP_REFERER'],
            'UTM_SOURCE' => $_COOKIE['UTM_SOURCE'] ? urldecode($_COOKIE['UTM_SOURCE']) : '',
            'UTM_MEDIUM' => $_COOKIE['UTM_MEDIUM'] ? urldecode($_COOKIE['UTM_MEDIUM']) : '',
            'UTM_TERM' => $_COOKIE['UTM_TERM'] ? urldecode($_COOKIE['UTM_TERM']) : '',
            'UTM_CONTENT' => $_COOKIE['UTM_CONTENT'] ? urldecode($_COOKIE['UTM_CONTENT']) : '',
            'UTM_CAMPAIGN' => $_COOKIE['UTM_CAMPAIGN'] ? urldecode($_COOKIE['UTM_CAMPAIGN']) : '',
            //'chanel'=> //渠道 TODO
        );
        $track_params = array_merge($track_params, $track_arr['track_params']);
        if(app::get('vmcocean')->getConf('debug_model') == 'enabled'){
            logger::alert($track_params);
        }
        vmc::singleton('vmcocean_stage')->track_event($uid, $event_name, $track_params);
    }
}
