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


class wechat_services_sendfinish
{


    /*
     * 订单发货通过微信通知
     */
    public function exec($delivery_sdf, &$msg)
    {
        $order_id = $delivery_sdf['order_id'];
        $mdl_xcxtplmsg = app::get('wechat')->model('xcxtplmsg');
        $msg_data = $mdl_xcxtplmsg->getRow('*',array('order_id'=>$order_id,'msg_type'=>'AT0007'));
        if(!$msg_data){
            return true;
        }
        // if(!$delivery_sdf['delivery_items']){
        //     $delivery_sdf['delivery_items'] = app::get('b2c')->model('delivery_items')->getList('*',array('delivery_id'=>$delivery_sdf['delivery_id']));
        // }
        $dlycorp = app::get('b2c')->model('dlycorp')->dump($delivery_sdf['dlycorp_id']);
        $env_list = array(
            'order_id'=>$delivery_sdf['order_id'],
            'consignee_name'=>$delivery_sdf['consignee']['name'],
            'consignee_addr'=>$delivery_sdf['consignee']['addr'],
            'consignee_mobile'=>$delivery_sdf['consignee']['mobile'],
            'dlycorp_name'=>$dlycorp['name'],
            'logistics_no'=>$delivery_sdf['logistics_no'],
            'time'=> $delivery_sdf['last_modify'] ?date('Y-m-d H:i:s',$delivery_sdf['last_modify']) :date('Y-m-d H:i:s') ,
        );
        $msg_data['data'] = array(
            'keyword1'=>array('value'=>$env_list['order_id']),//订单号
            'keyword2'=>array('value'=>$env_list['time']),//发货时间
            'keyword3'=>array('value'=>$env_list['dlycorp_name']),//物流公司
            'keyword4'=>array('value'=>$env_list['logistics_no']),//物流单号
            'keyword5'=>array('value'=>$env_list['consignee_name']),//收货人
            'keyword6'=>array('value'=>$env_list['consignee_addr']),//收货地址
            'keyword7'=>array('value'=>$env_list['consignee_mobile'])//收货联系方式
        );
        if($mdl_xcxtplmsg->save($msg_data) && vmc::singleton('wechat_xcxstage')->send_tplmsg($msg_data['id'],$error_msg)){
            logger::warning('小程序发货模板消息发送失败:'.$error_msg);
            logger::warning($msg_data);
        }
        return true;
    }



}
