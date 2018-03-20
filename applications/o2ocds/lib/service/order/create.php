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
class o2ocds_service_order_create
{
    public function __construct($app)
    {
        $this->tb_prefix = vmc::database()->prefix;
        $this->app = $app;
        $this->app_b2c = app::get('b2c');
    }

    /*
     * 订单创建前，处理二维码
     */
    public function exec(&$order, &$msg)
    {
        if(!$qrcode = $_POST['qrcode']) {
            $qrcode = $_COOKIE['qrcode'];
        };
        if($recommender_member_id = $_POST['_recommender_member_id']) {
            //判断分享的会员是否是店铺身份
            if($recommender_relation = $this->app->model('relation')->getRow('*',array('member_id'=>$recommender_member_id,'type'=>'store'))) {
                $order['recommender_relation']  = $recommender_relation;
            };
        }
        if($qrcode) {
            if($qrcode_id = $this->app->model('qrcode')->get_qrcode_id($qrcode,$msg)) {
                $order['qrcode_id'] = $qrcode_id;
            };
        };
        return true;
    }
}