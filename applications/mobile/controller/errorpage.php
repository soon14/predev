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


/*触屏错误 端首页入口*/
class mobile_ctl_errorpage extends mobile_controller{


    function index($params){
        $this->pagedata['params'] = $params;
        $this->display('errorpage.html');
    }

}
