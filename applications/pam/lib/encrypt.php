<?php
/**
* 这个类加载用户自定义密码加密方式
*/
class pam_encrypt{
	/**
	* 得到自定义加密方式的密文
	* @param string $password 密码明文
	* @param string $account_type 加密类型，对应的类文件
	* @return string 返回加密后的密文
	*/
    public static function get_encrypted_password($password,$account_type,$userdata=null){
        $encrypt = vmc::service('encrypt_'.$account_type);
        if(is_object($encrypt) && $userdata){
            if(method_exists($encrypt,'get_encrypted')){

            }
        }else{
            $encrypt = vmc::singleton('pam_encrypt_default');
        }
        return $encrypt->get_encrypted($password,$account_type,$userdata);
    }
}