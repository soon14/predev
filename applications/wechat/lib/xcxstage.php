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
class wechat_xcxstage
{
    // public function __construct()
    // {
    //
    // }

    /**
     *  @param $path 小程序路由地址（需已在 小程序app.json 定义)
     *  @param $type 二维码类型
     *  @param $width 二维码宽度
     *  @param $line_color 二维码颜色（小程序专用码有效）
     *  @param $local_cache 是否缓存到本地并且返回image_id
     */
    public function get_qrcode($path = '/pages/index/index',$type='normal',$width = 430,$line_color=false,$local_cache = false){
        $http_client = vmc::singleton('base_httpclient');
        $access_token = $this->get_access_token();
        if (!$access_token) {
            return false;
        }
        if(substr($path,0,1) == '/'){
            $path = substr_replace($path,'',0,1);
        }
        $input_params = array(
            'path'=>$path,
            'type'=>$type,
            'wdith'=>$wdith
        );

        $qrcode_target_md5 = utils::array_md5($input_params);
        if($local_cache){
            $mdl_xcxqrcode = app::get('wechat')->model('xcxqrcode');
            $exist_qrcode = $mdl_xcxqrcode->dump($qrcode_target_md5);
            if($exist_qrcode && $exist_qrcode['image_id']){
                return $exist_qrcode['image_id'];
            }
        }
        switch ($type) {
            case 'normal':
                //普通二维码
                $qrcode_action = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=$access_token";
                $qrcode_content = $http_client->post($qrcode_action, json_encode(array(
                    'path' => $path,
                    'width' => $width,
                )));
                break;
            case 'xcx':
                //小程序二维码（100000额度）
                $qrcode_action = "https://api.weixin.qq.com/wxa/getwxacode?access_token=$access_token";
                $params = array(
                    'path' => $path,
                    'width' => $width,
                );
                if(is_array($line_color)){
                    //{"r":"0","g":"0","b":"0"}
                    $params['line_color'] = $line_color;
                }
                $qrcode_content =  $http_client->post($qrcode_action, json_encode($params));
                break;
            case 'scene':
                //小程序场景二维码（暂无限制额度,但不支持path 直接带参数，参数需用scene传递scene 最长32
                //需要小程序内有 scene/redirect 页面支持
                $qrcode_action = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=$access_token";
                $scene_key = md5($path);
                $params = array(
                    'scene'=>$scene_key,
                    'page' => 'pages/scene/redirect',
                    'width' => $width,
                );
                if(is_array($line_color)){
                    //{"r":"0","g":"0","b":"0"}
                    $params['line_color'] = $line_color;
                }
                $mdl_xcxscene = app::get('wechat')->model('xcxscene');
                $new_scene = array(
                    'scene_key'=>$scene_key,
                    'scene_path'=>"/$path",
                    'createtime'=>time()
                );
                $mdl_xcxscene->save($new_scene);
                $qrcode_content =  $http_client->post($qrcode_action, json_encode($params));
                break;
        }

        if(!$local_cache){
            //无需缓存到本地,直接返回二维码文件内容
            return $qrcode_content;
        }else{

            $mdl_xcxqrcode = app::get('wechat')->model('xcxqrcode');
            $mal_image = app::get('image')->model('image');
            $tmp_file = tempnam(TMP_DIR, $image_name = $qrcode_target_md5.'.png');
            file_put_contents($tmp_file, $qrcode_content);
            $image_id = $mal_image->store($tmp_file,null,null,$image_name);
            @unlink($tmp_file);
            $new_xcxqrcode_cache = array(
                'qrcode_target_md5'=>$qrcode_target_md5,
                'image_id'=>$image_id,
                'qrcode_path'=>$path,
                'qrcode_type'=>$type,
                'qrcode_width'=>$width,
                'createtime'=>time()
            );
            $mdl_xcxqrcode->save($new_xcxqrcode_cache);
            return $image_id;
        }



    }
    /**
     *  @param $path 小程序路由地址（需已在 小程序app.json 定义)
     */
    public function get_qrcode_url($path)
    {
        $kv = base_kvstore::instance('wechat_wxxcx_qrcodestr');
        if ($kv->fetch(md5($path),$barcode_data)) {
            return $barcode_data;
        }
        $res = $this->get_qrcode($path,300);
        //header('Content-type: image/png');
        //echo $res;exit;
        /*
         *  PHP extension for reading barcodes. Uses ImageMagick(http://www.imagemagick.org/) for image support and zbar(http://zbar.sourceforge.net/) for scanning the barcodes. http://valokuva.org
         *  https://github.com/mkoppanen/php-zbarcode
         */
        $tmp_qrcodefile = $this->tmp_qrcodefile = tempnam(TMP_DIR, 'tmp_wxxcx_qrcode');
        file_put_contents($tmp_qrcodefile, $res);
        $image = new ZBarCodeImage($tmp_qrcodefile);
        $scanner = new ZBarCodeScanner();
        $barcode = $scanner->scan($image);
        unlink($tmp_qrcodefile);
        //vmc::dump($barcode);
        if (!$barcode[0] || !$barcode[0]['data']) {
            return false;
        }
        $kv->store(md5($path), $barcode[0]['data']);
        return $barcode[0]['data'];
    }

    private function get_access_token()
    {
        $app_id = app::get('wechat')->getConf('wxxcx_appid');
        $app_secret = app::get('wechat')->getConf('wxxcx_appsecret');

        if (
                defined('IS_SAAS_APP') &&
                IS_SAAS_APP &&
                defined('IS_SAAS_ON_APP') &&
                IS_SAAS_ON_APP &&
                defined('SAAS_CLIENT_ID') &&
                SAAS_CLIENT_ID &&
                defined('SAAS_ORDER_ID') &&
                SAAS_ORDER_ID &&
                class_exists('open_sys')
        ) {
            $query = array();
            $query['app_type'] = SAAS_APP_TYPE;
            $query['app_norm'] = SAAS_APP_VER;
            $query['client_id'] = SAAS_CLIENT_ID;
            $query['order_id'] = SAAS_ORDER_ID;
            $query['open_type'] = 'wx';
            $query['open_app_id'] = $app_id;

            $api_get = vmc::singleton('open_sys')->call_openapi('open', 'server', 'get_access_token', $query);
            if ($api_get && $api_get['result'] == 'success' && $api_get['data']) {
                return $api_get['data'];
            }
            return false;
        }

        $http_client = vmc::singleton('base_httpclient');
        if (!cacheobject::get('wechat_access_token_'.$app_id, $access_token) || !$access_token) {
            $access_token_action = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$app_id&secret=$app_secret";
            $respnese = $http_client->get($access_token_action);
            $respnese = json_decode($respnese, 1);
            $access_token = $respnese['access_token'];
            if ($access_token) {
                cacheobject::set('wechat_wxxcx_access_token_'.$app_id, $access_token, time() + $respnese['expires_in'] - 100);
            }
        }

        return $access_token;
    }

    public function send_tplmsg($id,&$error_msg){
        $access_token = $this->get_access_token();
        if(!$access_token)return;
        $mdl_xcxtplmsg = app::get('wechat')->model('xcxtplmsg');
        $msg_data = $mdl_xcxtplmsg->dump($id);
        $action_url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=$access_token";
        $http_client = vmc::singleton('base_httpclient');
        $post_data = array(
            'touser'=>$msg_data['touser'],
            'template_id'=>$msg_data['template_id'],
            'page'=>strpos($msg_data['page'] ,'/')===0 ? substr($msg_data['page'] ,1) : $msg_data['page'],
            'form_id'=>$msg_data['form_id'],
            'data'=>$msg_data['data']
        );
        if($msg_data['emphasis_keyword']){
            $post_data['emphasis_keyword'] = $msg_data['emphasis_keyword'];
        }
        $respnese = $http_client->post($action_url,json_encode($post_data));
        $respnese_data = json_decode($respnese, 1);
        $msg_data['send_log'] = $respnese;
        $msg_data['timestamp'] = time();
        if($respnese_data['errcode']>0){
            $error_msg = $respnese_data['errmsg'];
            $msg_data['send_status'] = 'error';
            $mdl_xcxtplmsg->save($msg_data);
            return false;
        }
        $msg_data['send_status'] = 'succ';
        $mdl_xcxtplmsg->save($msg_data);
        return true;
    }

}
