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


class toauth_viewhelper
{

    //拼接forword
    public function modifier_toauth_forward($auth_url,$forward){
        if(stristr($auth_url,'state=STATE')){
            $auth_url = str_replace('state=STATE','state='.urlencode($forward),$auth_url);
        }else{
            $auth_url = $auth_url.(stristr('?')?'&':'?').'forward='.urlencode($forward);
        }
        return $auth_url;
    }

}
