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
class requestfilter_service_csrf{
    public function __construct(){
        vmc::singleton('base_session')->start();
    }

    public function exec($request){
        if(app::get('requestfilter')->getConf('csrf_filter') =='true'){
            $csrf = vmc::singleton('requestfilter_csrf');
            $exist_token = $csrf->get_token();
            if($exist_token){
                $params = $request->get_params(true);
                $token = $params['_token'] ?: $request->get_header('X-CSRF-TOKEN');
                if($request->is_post() && !$csrf ->token_match($exist_token ,$token)){
                    logger::error('csrf非法请求,请求地址：'.$request->get_request_uri());
                    trigger_error('非法请求',E_USER_ERROR);
                }
            }else{
                $csrf ->set_token();
            }
        }
    }
}