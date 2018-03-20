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


class logisticstrack_puller {
    var $support_corps = array(
        'EMS' => 'ems',
        'STO' => 'shentong',
        'YTO' => 'yuantong',
        'SF' => 'shunfeng',
        'YUNDA' => 'yunda',
        'APEX' => 'quanyikuaidi',
        'LBEX' => 'longbanwuliu',
        'ZJS' => 'zhaijisong',
        'TTKDEX' => 'tiantian',
        'ZTO' => 'zhongtong',
        'HTKY' => 'huitongkuaidi',
        'CNMH' => 'minghangkuaidi',
        'AIRFEX' => 'yafengsudi',
        'CNKJ' => 'kuaijiesudi',
        'DDS' => 'dsukuaidi',
        'HOAU' => 'huayuwuliu',
        'CRE' => 'zhongtiewuliu',
        'FedEx' => 'fedex',
        'UPS' => 'ups',
        'DHL' => 'dhl',
        'CYEXP' => 'changyuwuliu',
        'DBL' => 'debangwuliu',
        'POST' => 'post',
        'CCES' => 'cces',
        'DTW' => 'datianwuliu',
        'ANTO' => 'andewuliu',
    );
    public function pull($delivery_id, &$msg, $nocache = false) {
        if (!$delivery_id) {
            $msg = '缺少参数:发货\退货单号';
            return false;
        }
        $mdl_delivery = app::get('b2c')->model('delivery');
        $delivery = $mdl_delivery->dump($delivery_id,'*',array(':dlycorp'=>array('*')));

        if (!$delivery['logistics_no'] || !$delivery['dlycorp']['corp_code']) {
            $msg = '物流单号或物流公司息不全';
            return false;
        }
        $logistics_no = $delivery['logistics_no'];
        $dlycorp = $delivery['dlycorp'];//sdf array
        $dlycorp_code = $dlycorp['corp_code'];//物流公司代码
        $dlycorp_name = $dlycorp['name'];//物流公司名称
        if (!$dlycorp_code) {
            $msg = '不支持' . $dlycorp_name . '物流状态同步';
            return false;
        }
        if (!$support_dlycorp_code = $this->dly_corp_support($dlycorp_code)) {
            $support_dlycorp_code = $dlycorp_code;
        }


        $mdl_logistic_log = app::get('logisticstrack')->model('logistic_log');
        $log = $mdl_logistic_log->getRow('*', array(
            'delivery_id' => $delivery_id
        ));
        $delay = app::get('logisticstrack')->getConf('kuaidi100delay');
        if(!$delay)$delay = 45;
        if ($nocache || !$log || ((time() - 60 * $delay) > $log['dtline'])) {
            $data = $this->pull_from_remote($logistics_no, $support_dlycorp_code, $msg);
            if (!$data) {
                $tmp_context = '单号：' . $logistics_no . '暂无物流跟踪记录。您也可以到' . $dlycorp_name . '<a href="' . $dlycorp['request_url'] . '" target="_blank">官网查询</a>';
                $data[] = array(
                    'time' => date('Y-m-d H:i:s'),
                    'context' => $tmp_context
                );
            }
            $new_log = array(
                'dly_corp' => $dlycorp_name,
                'logistic_no'=>$logistics_no,
                'delivery_id' => $delivery_id,
                'logistic_log' => serialize($data) ,
                'pulltimes' => (int)$log['pulltimes'] + 1,
                'dtline' => time() ,
            );
            $mdl_logistic_log->store($new_log, $delivery_id); //持久化到数据库

        } else {
            $data = unserialize($log['logistic_log']);
        }
        return array(
            'logi_no' => $logistics_no,
            'logi_log' => $data,
            'logi_name' => $dlycorp_name,
            'logi_code' => $dlycorp_code,
            'logi_py'=>$this->dly_corp_support($dlycorp_code)
        );
    }
    public function pull_from_remote($logi_no, $dlycorp_code, &$msg) {
        if(defined('KD_SOCKET') && constant('KD_SOCKET')){
            return $this ->query($logi_no, $dlycorp_code ,$msg);
        }
        $url = 'http://api.kuaidi100.com/api?';
        $logi_no = preg_replace('/\xEF\xBB\xBF/', '', $logi_no);
        $params = array(
            'id' => app::get('logisticstrack')->getConf('kuaidi100Key') ,
            'nu' => $logi_no,
            'com' => $dlycorp_code
        );
        $url.= http_build_query($params);
        $httpclient = new base_httpclient();
        $response = $httpclient->get($url);
        $response = json_decode($response, 1);
        $status = intval($response['status']);
        switch ($status) {
            case 0:
                $msg = '无结果';
                return false;
            case 1:
                //$msg = '跟踪成功';
                return $response['data'];
            default:
                $msg = 'API接口异常['.$response['message'].']';
                return false;
        }
    }
    private function dly_corp_support($corp_code) {
        return $this->support_corps[$corp_code];
    }

    private function query($logi_no, $dlycorp_code ,&$msg){
        $data = array(
            'logistic_no' =>$logi_no,
            'corp_code' =>$dlycorp_code,
            'token' =>CUSTOM_TOKEN
        );
        $res = vmc::singleton('base_httpclient') ->post(KD_URL.'get_logistic' ,$data);
        $res_arr = json_decode($res ,1);
        if($res_arr['result'] =='success'){
            return $res_arr['data']['logistic_log'];
        }
        $msg = $res_arr['msg'] ?$res_arr['msg'] :$res;
        return false;
    }
}
