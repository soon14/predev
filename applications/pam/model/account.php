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
* account model类
*/
class pam_mdl_account extends dbeav_model{
	/**
	* 关联MODEL
	* @var array
	*/
	var $has_many = array(
        'account'=>'account:append',
    );
	/**
	* dump 等操作的相关联表
	* @var array
	*/
    var $subSdf = array(
        'delete' => array(
            'account:account' => array('*'),
         )
    );
	
	/**
	 * 得到帐号用户名
	 * @param int $account_id 用户ID
	 * @return string 返回ID对应的用户名
	 */
	public function get_operactor_name($account_id='')
	{
		if ($account_id == '')
			return '未知或无操作员';
		
		$tmp = $this->getList('login_name',array('account_id'=>$account_id));
		if (!$tmp)
		{
			return '未知或无操作员';
		}
		
		return $tmp[0]['login_name'];
	}
}
