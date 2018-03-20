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


class wechat_mdl_xcxpage extends dbeav_model
{
    public function modifier_bg_hex($col){
        return "<span class='badge' style='border:1px #666 solid;background-color:$col'>$col</span>";
    }
    public function modifier_version($col){
        return 'V'.$col;
    }
}
