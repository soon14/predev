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
class logisticstrack_openapi_api extends base_openapi{

    function callback(){
        $params = vmc::singleton('base_component_request')->get_params(true);
        $mdl_log = app::get('logisticstrack') ->model('customer_logistic');
        $data = $params['param'];
    //    logger::error('快递100回调'.$data);
        $data = json_decode($data ,1);
        $result = $data['lastResult'];
        $logistic = $mdl_log ->getRow('*' ,array('logistic_no' =>$result['nu']));
        if($logistic){
            $logistic['logistic_log'] = serialize($result['data']);
            $logistic['message'] = $data['message'];
            $logistic['status'] = $data['status'];
            $logistic['dtline'] = time();
            $mdl_log ->save($logistic);
            $res = array(
                'result' =>true,
                "returnCode"=>"200",
	            "message"=>"提交成功"
            );
        }else{
            $res = array(
                'result' =>false,
                "returnCode"=>"200",
                "message"=>"没有查询到该快递单号"
            );
        }
        echo json_encode($res);
    }
}