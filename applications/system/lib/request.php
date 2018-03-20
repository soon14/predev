<?php
class system_request{

    function register($url,$params){
        return true;
    }

    static function gen_sign($params,$token){
        $str = self::assemble($params);
        logger::info('siyou_matrix：'.$str."====".$token);
        return strtoupper(md5(strtoupper(md5($str)).$token));
    }

    static function assemble($params)
    {
        if(!is_array($params))  return null;
        ksort($params, SORT_STRING);
        $sign = '';
        foreach($params AS $key=>$val){
            if(is_null($val))   continue;
            if(is_bool($val))   $val = ($val) ? 1 : 0;
            $sign .= $key . (is_array($val) ? self::assemble($val) : $val);
        }
        return $sign;
    }//End Function

}
