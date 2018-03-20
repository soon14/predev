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


class fastgroup_services_order_payfinish
{
    /**
     * 公开构造方法.
     *
     * @params app object
     */
    public function __construct($app)
    {
        $this->app = $app;
    }
    /**
     * 订单支付后的处理.
     *
     * @params array 支付完的信息
     * @params 支付时候成功的信息
     */
    public function exec(&$bill, &$msg = '')
    {
        if ($bill['status'] != 'succ' && $bill['status'] != 'progress') {
            $msg = '支付失败!';

            return false;
        }
        $order_id = $bill['order_id'];
        $mdl_fgorders = app::get('fastgroup')->model('fgorders');
        $fgorder = $mdl_fgorders->getRow('*', array('order_id' => $order_id));
        
        if (!$fgorder) {
            //没有相关快团订单,忽略，返回true;
            return true;
        }
        //同步状态
        $order = app::get('b2c')->model('orders')->dump($order_id);
        $fgorder['pay_status'] = $order['pay_status'];
        $fgorder['succ_pay_time'] = time();
        if (!$mdl_fgorders->save($fgorder)) {
            logger::error('订单支付信息同步到快团失败!ORDER_ID:'.$bill['order_id']);
            $msg = '订单支付异常.';

            return false;
        }
        $subject = app::get('fastgroup')->model('subject')->dump($fgorder['subject_id']);
        $payapp = app::get('ectools')->model('payment_applications')->dump($order['pay_app']);
            //发短信通知
            $env_list = array(
                'fg_title' => $subject['fg_title'],
                'payed' => $order['payed'],
                'payapp' => $payapp['display_name'],
                'succ_pay_time' => $fgorder['succ_pay_time'],
                'skey' => $fgorder['skey'],
            );
        vmc::singleton('b2c_messenger_stage')->trigger('fastgroup-payfinish', $env_list, array(
                'mobile' => $fgorder['mobile'],
            ));

        return true;
    }
}
