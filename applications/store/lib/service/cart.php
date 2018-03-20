<?php
/**
 * Created by PhpStorm.
 * User: wl
 * Date: 2016/1/23
 * Time: 18:16
 */

class store_service_cart{

    function get_seaport_id($products){
        foreach($products as $product_id =>$nums){
            $goodsextra= app::get('acrossborders')->model('productsfee') ->getRow('*' ,array('product_id' => $product_id));
            return $goodsextra['seaport_id'];
        }
        return 0;
    }

    /*
     * 获取最终的购物车
     */
    public function get_last_cart(&$checkout_result , $is_across = false){
        $products = vmc::singleton('acrossborders_checkout_filter') ->get_fee($checkout_result['cart_result'] ,$checkout_result['seaport_id']);
        foreach($checkout_result['cart_result']['objects']['goods'] as $k=>$product)
        {
            $product_info = $product['item']['product'];
            $checkout_result['cart_result']['objects']['goods'][$k]['item']['product'] = array_merge($product_info ,$products['item'][$product_info['product_id']]) ;
        }
        $mdl_rule =  app::get('acrossborders')->model('rule');
        $tax_threshold = $mdl_rule->getRow('tax_threshold',array('seaport_id'=>$checkout_result['seaport_id']));
        $tax_threshold = $tax_threshold['tax_threshold'];
        $seaport_total =$products['seaport_total']?$products['seaport_total'] :0;
        $checkout_result['total']['seaport_total'] = 0;
        if(!empty($tax_threshold) && ($seaport_total >= $tax_threshold))
        {
            $checkout_result['total']['seaport_total'] = $seaport_total;
            $checkout_result['total']['order_total'] +=$seaport_total;
        }
    }


    /*
     * 判断能否加入跨境购物车
     */

    public function check_products($products , $seaport_id=0){
        if($seaport_id){
            foreach ($products as $product_id => $num) {
                $product = app::get('b2c')->model('products') ->getRow('goods_id' ,array('product_id' => $product_id));
                $goodsextra= app::get('acrossborders')->model('productsfee') ->getRow('*' ,array('product_id' =>$product_id));
                if($goodsextra['seaport_id'] !=$seaport_id){
                    throw new Exception('商品['.$product['name'].']是不同口岸的商品');
                }
            }
        }
    }

}