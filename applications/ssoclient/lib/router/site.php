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
class ssoclient_router_site{
    public function exec($request){
        $app = $request ->get_app_name();
        $ctl = $request ->get_ctl_name();
        $act = $request ->get_act_name();
        if($app =='b2c' && $ctl=='site_passport' && in_array($act, array('index' , 'login' ,'logout' ,'signup'))){
            $redirect = app::get('site') ->router() ->gen_url(array(
                'app' =>'ssoclient',
                'ctl' =>'site_passport',
                'act' =>$act,
                'full' =>1
            ));
            if(!strpos($_SERVER['HTTP_REFERER'], 'passport')){
                $redirect  .='?forward='.$_SERVER['HTTP_REFERER'];
            }
            header('Location:'.$redirect);
        }
        vmc::singleton('ssoclient_member_sso') ->member_verify();
    }

}