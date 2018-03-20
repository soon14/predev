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
class solr_pinyin{

    public function __construct(){
        $this ->pinyin = new solr_pinyin_Pinyin();
    }
    public function zh_convert($string ,$split=''){
        $long = $this ->pinyin ->permalink($string, $split);
        $short = $this ->pinyin->abbr($string);
        return array(
            'l' =>$long,
            's' =>$short
        );
    }

    public function get_str_type($str){
        $str= trim($str);
        if(preg_match('/^[A-Z_a-z\s]+$/' ,$str)){
            return "1";//全英文
        }
        $lenA= strlen($str);
        $lenB= mb_strlen($str, "utf-8"); //文件的编码方式要是UTF8
        if($lenA=== $lenB) {
            return "4";//英文、数字
        }else {
            if($lenA % $lenB== 0) {
                return"2";//全中文
            }else {
                if(preg_match('/\d+/' ,$str)){
                    return "5";//中文、数字（英文）混合
                }
                return"3";//中英混合
            }
        }
    }
}