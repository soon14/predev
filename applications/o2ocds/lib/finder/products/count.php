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
class o2ocds_finder_products_count
{
    var $column_edit1 = '商品sku';
    var $column_edit2 = '商品名称';
    var $column_edit3 = '总订单数';
    var $column_edit4 = '总销售金额';
    var $column_edit5 = '供应商';

    function column_edit1($row)
    {
        $goods = app::get('b2c')->model('products')->getRow('bn', array('product_id' => $row['product_id']));

        return $goods['bn'];

    }

    function column_edit2($row)
    {
        $goods = app::get('b2c')->model('products')->getRow('name', array('product_id' => $row['product_id']));

        return $goods['name'];
    }

    function column_edit3($row)
    {
        $order_count = app::get('b2c')->model('order_items')->count(array('product_id' => $row['product_id']));

        return $order_count ? $order_count : 0;
    }

    function column_edit4($row)
    {
        $order = app::get('b2c')->model('order_items')->getRow('SUM((buy_price*nums)) as amount',
            array('product_id' => $row['product_id']));

        return $order['amount'] ? $order['amount'] : 0;
    }

    function column_edit5($row)
    {
        $app_seller = app::get("seller");
        if ($app_seller->is_installed()) {
            $goods_id = app::get('b2c')->model("products")->getRow("goods_id",
                array("product_id" => $row['product_id']));
            $supplier_id = $app_seller->model("goods")->getRow("supplier_id",
                array("goods_id" => $goods_id['goods_id']));
            $re = $app_seller->model("supplier")->getRow("supplier_name",
                array("supplier_id" => $supplier_id['supplier_id']));
            if (!empty($re)) {
                return $re['supplier_name'];
            }
        }

        return "自营";
    }

}