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


class package_service_order_create_before {
    /**
     * 构造方法
     * @param object app
     */
    public function __construct($app) {
        $this->app = $app;
        $this->obj_math = vmc::singleton('ectools_math');
    }

    public function exec(&$order_sdf, $cart_result = array() , &$msg = '') {
        //组织订单明细-[组合套餐内商品]
        foreach ($cart_result['objects']['package'] as $key => $object) {
            if ($object['disabled'] == 'true') {
                continue;
            }
            foreach($object['params']['package_goods'] as $product){
                $order_sdf['items'][] = array(
                    'order_id' => $order_sdf['order_id'],
                    'product_id' => $product['product_id'],
                    'goods_id' => $product['goods_id'],
                    'bn' => $product['bn'],
                    'name' => $product['name'],
                    'spec_info' => $product['spec_info'],
                    'price' => $product['price'],
                    'member_lv_price' => $product['member_lv_price'],
                    'buy_price' => $product['package_price'],
                    'amount' => $this->obj_math->number_multiple(array(
                        $product['package_price'],
                        $product['quantity'] * $object['quantity'],
                    )) ,
                    'nums' => $product['quantity'] * $object['quantity'],
                    'weight' => $this->obj_math->number_multiple(array(
                        $product['weight'],
                        $product['quantity'] * $object['quantity'],
                    )) ,
                    'image_id' => $product['image_id'],
                );
            }
        }
        return true;
    }
}
