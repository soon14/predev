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
class o2ocds_service_order_createfinish
{
    public function __construct($app)
    {
        $this->tb_prefix = vmc::database()->prefix;
        $this->app = $app;
        $this->app_b2c = app::get('b2c');
    }

    /*
     * 订单创建成功生成服务码，关联到分佣二对象
     */
    public function exec(&$order, &$msg)
    {
        $mdl_service_code = $this->app->model('service_code');
        $mdl_qrcode = $this->app->model('qrcode');
        $ratio = $this->app->getConf('servicecode_ratio');
        $service_data = array(
            'service_code' => $this->app->model('service_code')->apply_code(),
            'order_id' => $order['order_id'],
            'integral' => round($ratio/100*$order['score_g']),
            'status' => '0',
            'createtime' => $order['createtime'],
        );
        /*
         * 1.店铺身份的下单，无论什么途径进入，都归属所属店铺
         * 2.分享下单
         * 3.扫描二维码下单
         * */
        if($store_relation = $this->app->model('relation')->getRow('*',array('member_id'=>$order['member_id'],'type'=>'store'))) {
            $enterprise_id = $this->app->model('store')->getRow('enterprise_id',array('store_id'=>$store_relation['relation_id']));
            $service_data['store_id'] = $store_relation['relation_id'];
            $service_data['enterprise_id'] = $enterprise_id['enterprise_id'];
        }elseif($order['recommender_relation']) {
            $service_data['recommender_member_id'] = $order['recommender_relation']['member_id'];
            $service_data['store_id'] = $order['recommender_relation']['relation_id'];
            if($enterprise_id = $this->app->model('store')->getRow('enterprise_id',array('store_id'=>$order['recommender_relation']['relation_id']))) {
                $service_data['enterprise_id'] = $enterprise_id['enterprise_id'];
            };
        }elseif($qrcode = $mdl_qrcode->getRow('*',array('qrcode_id'=>$order['qrcode_id']))) {
            $service_data['store_id'] = $qrcode['store_id'];
            $service_data['enterprise_id'] = $qrcode['enterprise_id'];
        }
        if($mdl_service_code->save($service_data)) {
            $order['service_code'] = $service_data['service_code'];
        };
        return true;
    }
}