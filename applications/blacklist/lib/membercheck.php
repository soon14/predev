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
class blacklist_membercheck{
    public function exec($member_id, &$msg)
    {
        if(app::get('blacklist')->model('members')->count(array('member_id' =>$member_id))){
            $msg ='登录受限';
            return false;
        }
        return true;
    }
}