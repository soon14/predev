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


 
/**
* 后台控制器基类
*/
class content_admin_controller extends desktop_controller 
{

	/**
	* 构造方法
	* @param object $app app实例
	*/
    function __construct($app) 
    {
        parent::__construct($app);
        $this->_request = vmc::singleton('base_component_request');
        $this->_response = vmc::singleton('base_component_response');
    }//End Function


}//End Class
