<?php
/**
* 该类是用户自定义的加密类型 md5加密方式
*/
class pam_encrypt_default{
	/**
	* 获取加密类型后的密文
	* @param string $source_str 加密明文
	* @return string 返回加密密文
	*/
    public function get_encrypted($source_str,$account_type,$userdata=null)
	{

		if(!$userdata) return md5($source_str);
		if($userdata['createtime'])
		{
			return $this->extends_md5($source_str,$userdata['login_name'],$userdata['createtime']);
		}
		else
		{
            if( $account_type  == 'member' ){
                $pam_members_model = app::get('pam')->model('members');
                $pam_filter = array(
                    'login_account'=>$userdata['login_name'],
                );
                $rows = $pam_members_model->getList('*',$pam_filter,0,1);
            }else{
                $pam_account_model = app::get('pam')->model('account');
                $pam_filter = array(
                    'login_name'=>$userdata['login_name'],
                    'account_type' => $account_type,
                    'disabled' => 'false',
                );
                $rows = $pam_account_model->getList('*',$pam_filter,0,1);
            }

			if($rows[0])
			{
				if(substr($rows[0]['login_password'],0,1) !== 's')
				{
					return md5($source_str);
				}
				else
				{
					return $this->extends_md5($source_str,$userdata['login_name'],$rows[0]['createtime']);
				}

			}
			else
			{
				return false;
			}

		}
    }

	public function extends_md5($source_str,$username,$createtime)
	{
		$string_md5 = md5(md5($source_str).$username.$createtime);
		$front_string = substr($string_md5,0,31);
		$end_string = 's'.$front_string;
		return $end_string;
	}
}
