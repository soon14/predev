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
return array(
    // 默认
    '__' => array(
        'fields' => array(
            // goods_id gid name brief category description specs products
            'goods_id' => 'goods_id',
            'goods_gid' => 'gid',
            'goods_name' => 'name',
            'goods_brief' => 'brief',
            'category_id' => 'cat_id',
            'marketable' => 'marketable',
            'description' => 'description',
            'specs' => 'specs',
            'products' => 'products',
        ),
        'input' => array(
        ),
        'output' => array(
        ),
    ),
    // goods.write.add - 新增商品 
    'write_add' => array(
        'fields' => array(
            'create_time' => 'create_time',
            'goods_id' => 'goods_id',
            'skus' => 'skus',
        ),
        'input' => array(
            'goods_gid' => 'gid',
            'goods_name' => 'name',
            'goods_brief' => 'brief',
            'category_id' => 'cat_id',
            'ext_cat' => 'extended_cat',
            'marketable' => 'marketable',
            'description' => 'description',
            'brand' => 'brand',
            //
            'specs' => 'specs',
            'products' => 'products',
            //
            'bn' => 'bn',
            'barcode' => 'barcode',
            'price' => 'price',
            'mktprice' => 'mktprice',
            'weight' => 'weight',
            'unit' => 'unit',
            'spec' => 'spec',
            // 
            'images' => 'images',
            'default_img' => 'default_img',
            // 
            'seo_title' => 'seo_title',
            'seo_keywords' => 'seo_keywords',
            'seo_description' => 'seo_description',
            'keywords' => 'keywords',
            //
            'nostore_sell' => 'nostore_sell',
            'gain_score' => 'gain_score',
            // 
            'type' => 'type',
            'type_props' => 'type_props',
            'type_params' => 'type_params',
            // 
            'goods_type' => 'goods_type',
        ),
        'output' => array(
        ),
    ),
    // goods.write.update - 修改商品 
    'write_update' => array(
        'fields' => array(
            'modified' => 'modified',
            'goods_id' => 'goods_id',
            'skus' => 'skus',
        ),
        'input' => array(
            'goods_id' => 'goods_id',
            'goods_gid' => 'gid',
            'goods_name' => 'name',
            'goods_brief' => 'brief',
            'category_id' => 'cat_id',
            'ext_cat' => 'extended_cat',
            'marketable' => 'marketable',
            'description' => 'description',
            'brand' => 'brand',
            //
            'specs' => 'specs',
            'products' => 'products',
            //
            'bn' => 'bn',
            'barcode' => 'barcode',
            'price' => 'price',
            'mktprice' => 'mktprice',
            'weight' => 'weight',
            'unit' => 'unit',
            'spec' => 'spec',
            // 
            'images' => 'images',
            'default_img' => 'default_img',
            // 
            'seo_title' => 'seo_title',
            'seo_keywords' => 'seo_keywords',
            'seo_description' => 'seo_description',
            'keywords' => 'keywords',
            //
            'nostore_sell' => 'nostore_sell',
            'gain_score' => 'gain_score',
            // 
            'type' => 'type',
            'type_props' => 'type_props',
            'type_params' => 'type_params',
            // 
            'goods_type' => 'goods_type',
        ),
        'output' => array(
        ),
    ),
    // goods.write.upOrDown - 商品上下架 
    'write_upOrDown' => array(
        'fields' => array(
            'modified' => 'modified',
            'goods_id' => 'goods_id',
        ),
        'input' => array(
            'goods_id' => 'goods_id',
            'op_type' => 'op_type',
        ),
        'output' => array(
        ),
    ),
    // goods.read.byId - 获取单个商品 
    'read_byId' => array(
        'fields' => array(
            'goods_id' => 'goods_id',
            'goods_gid' => 'gid',
            'goods_name' => 'name',
            'goods_category' => 'category',
            'goods_brand' => 'brand',
            'goods_brief' => 'brief',
            'marketable' => 'marketable',
            'description' => 'description',
            'specs' => 'spec_desc',
            'products' => 'product',
            'goods_type' => 'type',
            'goods_props' => 'props',
        ),
        'input' => array(
            'goods_id' => 'goods_id',
            'fields' => 'fields',
        ),
        'output' => array(
        ),
    ),
    // goods.sku.read.findSkuById - 获取单个SKU 
    'sku_read_findSkuById' => array(
        'fields' => array(
            'product_id' => 'product_id',
            'goods_id' => 'goods_id',
            'sku_id' => 'bn',
            'product_barcode' => 'barcode',
            'product_name' => 'name',
            'product_price' => 'price',
            'product_mktprice' => 'mktprice',
            'product_weight' => 'weight',
            'product_unit' => 'unit',
            'spec_info' => 'spec_info',
            'spec_desc' => 'spec_desc',
            'marketable' => 'marketable',
        ),
        'input' => array(
            'sku_id' => 'sku_id',
            'fields' => 'fields',
        ),
        'output' => array(
        ),
    ),
    // goods.sku.stock.read.find - 获取sku库存信息 
    'sku_stock_read_find' => array(
        'fields' => array(
            'stock_id' => 'stock_id',
            'product_name' => 'title',
            'sku_id' => 'sku_bn',
            'product_barcode' => 'barcode',
            'quantity' => 'quantity',
        ),
        'input' => array(
            'sku_id' => 'sku_id',
            'fields' => 'fields',
        ),
        'output' => array(
        ),
    ),
    // goods.sku.stock.write.update - 设置sku库存 
    'sku_stock_write_update' => array(
        'fields' => array(
        ),
        'input' => array(
            'sku_id' => 'sku_id',
            'quantity' => 'quantity',
        ),
        'output' => array(
        ),
    ),
);
