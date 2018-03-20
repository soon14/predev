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
class requestfilter_csrf{
    public function __construct(){
        vmc::singleton('base_session')->start();
    }

    public function get_token(){
        return $_SESSION['csrf_token'];
    }

    public function set_token(){
        $token  = $this ->random(40);
        $_SESSION['csrf_token'] = $token;
    }


    public function token_match($knownString, $userInput)
    {
        $knownString = (string) $knownString;
        $userInput = (string) $userInput;
        if (function_exists('hash_equals')) {
            return hash_equals($knownString, $userInput);
        }
        $knownLen = strlen($knownString);
        $userLen = strlen($userInput);
        $knownString .= $userInput;
        $result = $knownLen - $userLen;
        for ($i = 0; $i < $userLen; $i++) {
            $result |= (ord($knownString[$i]) ^ ord($userInput[$i]));
        }
        return 0 === $result;
    }


    private  function random($length = 16)
    {
        $bytes = openssl_random_pseudo_bytes($length * 2);
        if ($bytes === false)
        {
            return false;
        }
        return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
    }

    private function equals($knownString, $userInput)
    {
        $knownString = (string) $knownString;
        $userInput = (string) $userInput;

        if (function_exists('hash_equals')) {
            return hash_equals($knownString, $userInput);
        }

        $knownLen = strlen($knownString);
        $userLen = strlen($userInput);

        // Extend the known string to avoid uninitialized string offsets
        $knownString .= $userInput;

        // Set the result to the difference between the lengths
        $result = $knownLen - $userLen;

        // Note that we ALWAYS iterate over the user-supplied length
        // This is to mitigate leaking length information
        for ($i = 0; $i < $userLen; $i++) {
            $result |= (ord($knownString[$i]) ^ ord($userInput[$i]));
        }

        // They are only identical strings if $result is exactly 0...
        return 0 === $result;
    }
}