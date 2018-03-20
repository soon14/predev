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


class preselling_order_create
{
    /**
     * 构造方法.
     *
     * @param object app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->obj_math = vmc::singleton('ectools_math');
    }
    /**
     * 预售订单标准数据生成.
     */
    public function generate(&$order_sdf,$activity,&$msg = '')
    {
        $new_order_id = $order_sdf['order_id'] ? $order_sdf['order_id'] : app::get('preselling')->model('orders')->apply_id();

        $product = app::get('b2c')->model('products')->getRow('*',array('product_id'=>$order_sdf['product_id']));
        $current_product = $activity['conditions'][$order_sdf['product_id']];

        $balance_payment = $this->obj_math->number_minus(array($current_product['presell_price'],$current_product['deposit_deduction']));
        //定金金额
        $deposit_price =  $this->obj_math->number_multiple(array($order_sdf['nums'],$current_product['deposit']));
        //尾款金额
        $balance_payment_total =  $this->obj_math->number_multiple(array($order_sdf['nums'],$balance_payment));
        //预售金额
        $order_total =  $this->obj_math->number_plus(array($deposit_price,$balance_payment_total,$current_product['freight']));
        //单商品购买价格
        $buy_price = $this->obj_math->number_plus(array($balance_payment,$current_product['deposit']));
        $sdf = array(
            'presell_id' => $new_order_id, //预售订单唯一ID
            'ip' => base_request::get_remote_addr() , //下单IP地址
            'order_total' => $order_total, //预售订单应付当前货币总额
            'deposit_price' => $deposit_price,//定金
            'deposit_deduction' => $current_product['deposit_deduction'],
            'balance_payment' =>  $balance_payment_total,//尾款
            'cost_freight' => $current_product['freight'], //运费
            'product_id' => $product['product_id'],
            'goods_id' => $product['goods_id'],
            'bn' => $product['bn'],
            'barcode' => $product['barcode'],
            'name' => $product['name'],
            'spec_info' => $product['spec_info'],
            'price' => $product['price'],
            'buy_price' => $buy_price,
            'balance_starttime' => $activity['balance_starttime'],
            'balance_endtime' => $activity['balance_endtime'],
            'amount' => $this->obj_math->number_multiple(array($buy_price,$order_sdf['nums'])) ,
            'nums' => $order_sdf['nums'],
            'weight' => $this->obj_math->number_multiple(array($product['weight'],$order_sdf['nums'])),
            'image_id' => $product['image_id'],
        );
        $order_sdf = array_merge($order_sdf, $sdf);
        // 预售订单创建前之行的方法
        $services = vmc::servicelist('preselling.order.create.before');
        if ($services) {
            foreach ($services as $service) {
                $flag = $service->exec($order_sdf,$activity , $msg);
                if (!$flag) {
                    return false;
                }
            }
        }

        return true;
    }


    /**
     * 预售订单保存.
     *
     * @param array order_sdf
     * @param string message
     *
     * @return bool
     */
    public function save(&$sdf, &$msg = '')
    {
        $mdl_order = $this->app->model('orders');
        //must Insert
        $result = $mdl_order->save($sdf);
        if (!$result) {
            $msg = ('预售订单未能保存成功');
            return false;
        } else {
            //预售订单创建时同步扩展服务
            foreach (vmc::servicelist('preselling.order.create.finish') as $service) {
                if (!$service->exec($sdf, $msg)) {
                    //记录日志，不中断
                    logger::error($sdf['presell_id'].'创建出错！'.$msg);
                }
            }

            return true;
        }
    }
}
