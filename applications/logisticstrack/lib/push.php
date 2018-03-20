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
class logisticstrack_push {

    private $url ='http://www.kuaidi100.com/poll';
    private $query_url = 'http://poll.kuaidi100.com/poll/query.do';
    private $callback_url;
    public function __construct(&$app){
        $this ->app =$app;
        $this ->callback_url = vmc::openapi_url('openapi.logisticapi', 'callback');
        $this ->key = $this ->app ->getConf('kuaidi100Key');
        $this ->http = vmc::singleton('base_httpclient');
    }
    public function push_logistic($params ,&$msg){
        $param =array (
            'company' => strtolower($params['corp_code']),//订阅的快递公司的编码，一律用小写字母
            'number' => $params['logistic_no'],
            'to' => $params['address']? $params['address']:'',
            'key' => $this ->key,
            'parameters' =>
                array (
                    'callbackurl' => $this ->callback_url,
                    'resultv2' => '1',
                    'mobiletelephone' =>$params['mobile']?$params['mobile']:'',
                    'seller' =>'VMCSHOP'
                ),
        );
        $data =array(
            'schema' =>'json',
            'param' =>json_encode($param)
        );
        $res =$this ->http ->post($this ->url ,$data);
        $res_arr = json_decode($res ,1);
        if($res_arr){
            if($res_arr['returnCode'] =='200'){
                return true;
            }else{
                $msg = $res_arr['message'];
                return false;
            }
        }else{
            $msg = $res;
            return false;
        }

    }

    public function query($params ,&$msg){
        $param=array(
            "com" =>strtolower($params['corp_code']),//查询的快递公司的编码,一律用小写字母
            'num' =>$params['logistic_no'],
        );
        $param = json_encode($param);
        $customer = $this ->app ->getConf('customer');
        $data = array(
            'customer' =>$customer,
            'param' =>$param,
            'sign' =>md5($param.($this ->key).$customer)
        );
        $res =$this ->http ->post($this ->query_url ,$data);
        $res_arr = json_decode($res ,1);
        if($res_arr){
            return $res_arr;
        }else{
            $msg = $res;
            return false;
        }
    }
}