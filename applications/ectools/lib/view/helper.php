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


class ectools_view_helper
{
    public function __construct($app)
    {
        $this->app = $app;
    }
//    public function modifier_barcode($data,$height = 60)
//    {
//        return vmc::singleton('ectools_barcode_core_code128')->draw($data,$height);
//    }

    public function modifier_barcode($v ,$x){
        $url= vmc::openapi_url('openapi.barcode', 'encode', array('text' => $v ,'x'=>$x));
        return "<img src='$url' class='x-barcode' />";
    }
    public function modifier_qrcode($code_txt, $size = 5, $margin = 5)
    {
        $url = vmc::openapi_url('openapi.qrcode', 'encode', array('size' => $size, 'margin' => $margin)).'?txt='.urlencode($code_txt);

        return "<img src='$url' class='x-qrcode'/>";
    }
    public function modifier_payname($data)
    {
        return $this->app->model('payment_cfgs')->get_app_display_name($data);
    }

    public function modifier_rmb_chinese($price) {
        $price = round($price, 2);
        static $cnums=array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖"),
        $cnyunits=array("圆","角","分"),
        $grees=array("拾","佰","仟","万","拾","佰","仟","亿");
        list($ns1,$ns2)=explode(".",$price,2);
        $ns2=array_filter(array($ns2[1],$ns2[0]));
        $ret=array_merge($ns2,array(implode("",$this->_cny_map_unit(str_split($ns1),$grees)),""));
        $ret=implode("",array_reverse($this->_cny_map_unit($ret,$cnyunits)));
        return str_replace(array_keys($cnums),$cnums,$ret);
    }
    function _cny_map_unit($list,$units) {
        $ul=count($units);
        $xs=array();
        foreach (array_reverse($list) as $x) {
            $l=count($xs);
            if ($x!="0" || !($l%4)) $n=($x=='0'?'':$x).($units[($l-1)%$ul]);
            else $n=is_numeric($xs[0][0])?$x:'';
            array_unshift($xs,$n);
        }
        return $xs;
    }
	
	function block_app_status($params, $content, &$tpl){
        $status = app::get($params['name'])->status();
        if($status != $params['status']){
            return null;
           
        }
        return $content;
    }
}
