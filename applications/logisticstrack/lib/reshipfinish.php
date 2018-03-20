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




class logisticstrack_reshipfinish
{

    public function __construct($app)
    {
        $this->app = $app;
        $this->mdl_delivery = app::get('b2c')->model('delivery');
    }

    /**
     * 订单退货操作完成时
     * @params array - 退货单据数据SDF
     * @return boolean - 执行成功与否
     */
    public function exec($delivery_sdf,&$msg='')
    {
        if(!defined('KD_SOCKET') ||!constant('KD_SOCKET')){
            return true;
        }
        $delivery_id = $delivery_sdf['delivery_id'];
        if(!$delivery_id){
            $msg = '未知发货单id';
            return false;
        }
        $delivery = $this->mdl_delivery->dump($delivery_id);
        if(!$delivery['logistics_no']){
            $msg = '未知快递单号';
            return false;
        }
        $dlycorp_id = $delivery['dlycorp_id'];
        $dlycorp = app::get('b2c') ->model('dlycorp') ->dump($dlycorp_id);
        $data =array(
            'logistic_no' =>$delivery['logistics_no'],
            'corp_code' =>$dlycorp['corp_code'],
            'dly_corp' =>$dlycorp['name'],
            'mobile' => $delivery['consignor']['mobile'],
            'token' =>VMCSHOP_TOKEN
        );
        $res = vmc::singleton('base_httpclient') ->post(KD_URL.'set_logistic' ,$data);
        $res_arr = json_decode($res ,1);
        if($res_arr['result'] =='success'){
            return true;
        }
        $msg = $res_arr['msg'] ?$res_arr['msg'] :$res;
        return false;
    }


}
