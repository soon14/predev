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



interface ectools_payment_interface
{


	/**
	 * 设置后台的显示项目（表单项目）
	 * @params null
	 * @return array - 配置的表单项
	 */
	public function setting();


	/**
	 * 支付表单的提交方式
	 * @params array - 提交的表单数据
	 * @return html - 自动提交的表单
	 */
	public function dopay($payments,&$msg);



	/**
	 * 支付后返回后处理的事件的动作
	 * @params array - 所有返回的参数，包括POST和GET
	 * @return null
	 */
	public function callback(&$recv);

    /**
     * 支付后异步通知返回后处理的事件的动作
     * @params array - 所有返回的参数，包括POST和GET
     * @return null
     */
    public function notify(&$recv);


}

?>
