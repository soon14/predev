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

class commission_ctl_mobile_share extends base_controller
{
    //我的分享二维码
    public function myqrcode(){
        $mobile_url=($_SERVER['REQUEST_SCHEME']?$_SERVER['REQUEST_SCHEME']:'http').'://'.$_SERVER['HTTP_HOST'];
        $qrcode_image = vmc::singleton('wechat_qrcode')->create($mobile_url);
        $img_url = base_storager::image_path($qrcode_image['image_id']);
        $this->pagedata['img'] = $img_url;
        $this->page("mobile/share/myqrcode.html");
    }
}