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


class gift_order_createbefore
{
    public function __construct($app)
    {
        $this->obj_math = vmc::singleton('ectools_math');
    }

    public function exec(&$order_sdf, $cart_result = array(), &$msg = '')
    {
        //商品级赠品
        foreach ($cart_result['objects']['goods'] as $key => $object) {
            if ($object['disabled'] == 'true') {
                continue;
            }
            $this->_inject_order_items($order_sdf, $object['gift'], $object);
        }

        //订单级赠品
        if ($cart_result['gift']) {
            $this->_inject_order_items($order_sdf, $cart_result['gift']);
        }

        return true;
    }

    /**
     * 将购物车赠品信息处理，并插入到订单明细中.
     *
     * @param $order_sdf 订单标准数据数组, save 方法用
     * @param gift_arr 购物车赠品数组
     * @param $cart_object 购物车商品项
     *
     * @return bool
     */
    private function _inject_order_items(&$order_sdf, $gift_arr, $cart_object = false)
    {
        $mdl_products = app::get('b2c')->model('products');
        if (!$gift_arr || empty($gift_arr)) {
            return false;
        }
        $gift_combine_arr = array();
        foreach ($gift_arr as $gift) {
            if ($gift_combine_arr[$gift['product_id']]) {
                $gift_combine_arr[$gift['product_id']] = array(
                    'product_id' => $gift['product_id'],
                    'quantity' => $gift_combine_arr[$gift['product_id']]['quantity'] + $gift['quantity'],
                );
            } else {
                $gift_combine_arr[$gift['product_id']] = array(
                    'product_id' => $gift['product_id'],
                    'quantity' => $gift['quantity'],
                );
            }
        }
        $products = $mdl_products->getList('*', array('product_id' => array_keys($gift_combine_arr)));
        if (!$products) {
            logger::warning('赠品赠送异常'.__LINE__);

            return false;
        }
        foreach ($products as $gift_product) {
            //扩展订单明细，将赠品添加进订单明细
            $order_sdf['items'][] = array(
                'order_id' => $order_sdf['items'][0]['order_id'],
                'product_id' => $gift_product['product_id'],
                'releted_product_id' => $cart_object ? $cart_object['item']['product']['product_id'] : null,
                'goods_id' => $gift_product['goods_id'],
                'bn' => $gift_product['bn'],
                'name' => '[赠品]'.$gift_product['name'],
                'spec_info' => $gift_product['spec_info'],
                'price' => $gift_product['price'],
                'member_lv_price' => 0,
                'buy_price' => 0,
                'amount' => 0 ,
                'nums' => $gift_combine_arr[$gift_product['product_id']]['quantity'],
                'weight' => $this->obj_math->number_multiple(array(
                    $gift_product['weight'],
                    $gift_combine_arr[$gift_product['product_id']]['quantity'],
                )) ,
                'image_id' => $gift_product['image_id'],
                'item_type'=>'gift'
            );
        }

        return true;
    }
}
