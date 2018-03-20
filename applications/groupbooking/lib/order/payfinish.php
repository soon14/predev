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


class groupbooking_order_payfinish
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
     * 拼团订单支付后的处理.
     * @params array 支付完的信息
     * @params 支付时候成功的信息
     */
    public function exec(&$bill, &$msg = '')
    {
        logger::debug('拼团订单:'.$bill['order_id'] . 'payfinish exec');
        if ($bill['status'] != 'succ' && $bill['status'] != 'progress') {
            $msg = '支付其实没有完成!';

            return false;
        }
        $gb_id = $bill['order_id'];
        if (!$gb_id) {
            $msg = '未知拼团订单ID';

            return false;
        }
        $omath = vmc::singleton('ectools_math');
        $mdl_orders = $this->app->model('orders');

        $order_sdf = $mdl_orders->dump($gb_id);
        if (!$order_sdf) {
            $msg = '未知拼团订单';

            return false;
        }
        if ($order_sdf['pay_status'] == '1') {
            $msg = '重复在支付拼团订单' . date('Y-m-d H:i:s');

            return false;
        }
        if ($order_sdf['pay_status'] == '2' && $bill['status'] == 'progress') {
            $msg = '重复在支付拼团订单' . date('Y-m-d H:i:s');

            return false;
        }
        $payed = $omath->number_plus(array(
            $bill['money'],
            $order_sdf['payed'],
        ));
        $update['payed'] = $payed;
        switch ($bill['status']) {
            case 'succ':
                $update['pay_status'] = '1'; //支付完成
                if ($payed < $order_sdf['order_total']) {
                    $update['pay_status'] = '3'; //部分支付
                }
                if (!$mdl_orders->update($update, array('gb_id' => $gb_id))) {
                    $msg = '拼团订单单据信息更新失败!';
                    return false;
                }
                //支付完成，检查是否支付人数达到团的人数
                if($update['pay_status'] == '1'){
                    $activity = $this->app->model('activity')->getRow('people_number',array('activity_id'=>$order_sdf['activity_id']));
                    $pay_people = 1;
                    if($order_sdf['main_id'] != '0') { //子订单支付
                        $pay_order_count = $this->app->model('orders')->count(
                            array('main_id'=>$order_sdf['main_id'],'activity_id'=>$order_sdf['activity_id'],'status'=>'0','pay_status|in'=>array('1','2'))
                        );
                        $pay_people += $pay_order_count;
                        $main_id = $order_sdf['main_id'];
                    }else{
                        $main_id = $order_sdf['gb_id'];
                    }
                    if($activity['people_number'] == $pay_people) {
                        if(!vmc::singleton('groupbooking_order_createorder')->exec(array('main_id'=>$main_id),$msg)) {
                            return false;
                        };
                    }
                }
                break;
            case 'progress':
                $update['pay_status'] = '2'; //付款到了担保方
                if (!$mdl_orders->update($update, array('gb_id' => $gb_id))) {
                    $msg = '拼团订单单据信息更新失败!';
                    return false;
                }
                break;
            default:
                return false;
        }

        return true;
    }


}
