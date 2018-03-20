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


class groupbooking_order_create
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
     * 拼团订单标准数据生成.
     */
    public function generate(&$order_sdf,$activity,&$msg = '')
    {
        $new_order_id = $order_sdf['order_id'] ? $order_sdf['order_id'] : app::get('groupbooking')->model('orders')->apply_id();

        $product = app::get('b2c')->model('products')->getRow('*',array('product_id'=>$order_sdf['product_id'],'marketable' => 'true'));
        if($activity['restrict_status']) {
            $order_sdf['nums'] = $order_sdf['nums'] <= $activity['restrict_number']?$order_sdf['nums']:$activity['restrict_number'];
        }
        $current_product = $activity['conditions'][$order_sdf['product_id']];
        $order_total = $amount = $this->obj_math->number_multiple(array($order_sdf['nums'],$current_product['price']));
        if($current_product['freight'] > 0) {
            $order_total = $this->obj_math->number_plus(array($order_total,$current_product['freight']));
        }
        $sdf = array(
            'gb_id' => $new_order_id, //拼团订单唯一ID
            'ip' => base_request::get_remote_addr() , //下单IP地址
            'order_total' => $order_total, //拼团订单应付当前货币总额
            'cost_freight' => $current_product['freight'], //运费
            'product_id' => $product['product_id'],
            'goods_id' => $product['goods_id'],
            'bn' => $product['bn'],
            'barcode' => $product['barcode'],
            'name' => $product['name'],
            'spec_info' => $product['spec_info'],
            'price' => $product['price'],
            'buy_price' => $current_product['price'],
            'amount' => $amount ,
            'nums' => $order_sdf['nums'],
            'weight' => $this->obj_math->number_multiple(array($product['weight'],$order_sdf['nums'])),
            'image_id' => $product['image_id'],
        );
        $order_sdf = array_merge($order_sdf, $sdf);

        //TODO  优惠券数据
        // 拼团订单创建前之行的方法
        $services = vmc::servicelist('groupbooking.order.create.before');
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
     * 拼团订单保存.
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
            $msg = ('拼团订单未能保存成功');
            return false;
        } else {
            //拼团订单创建时同步扩展服务
            foreach (vmc::servicelist('groupbooking.order.create.finish') as $service) {
                if (!$service->exec($sdf, $msg)) {
                    //记录日志，不中断
                    logger::error($sdf['gb_id'].'创建出错！'.$msg);
                }
            }

            return true;
        }
    }
}
