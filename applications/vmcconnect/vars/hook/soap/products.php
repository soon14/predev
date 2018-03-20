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
            'product_id' => 'product_id',
            'goods_id' => 'goods_id',
            'sku_id' => 'bn',
            'product_code' => 'barcode',
            'product_name' => 'name',
            'product_price' => 'price',
            'mkt_price' => 'mktprice',
            'product_weight' => 'weight',
            'product_unit' => 'unit',
            'spec_info' => 'spec_info',
            'spec_desc' => 'spec_desc',
            'is_default' => 'is_default',
            'market_able' => 'marketable',
        ),
        'output' => array(
        ),
    ),
    // products.create - 创建产品 
    'create' => array(
        'fields' => array(
            'product_id' => 'product_id',
            'goods_id' => 'goods_id',
            'sku_id' => 'bn',
            'product_code' => 'barcode',
            'product_name' => 'name',
            'product_price' => 'price',
            'mkt_price' => 'mktprice',
            'product_weight' => 'weight',
            'product_unit' => 'unit',
            'spec_info' => 'spec_info',
            'spec_desc' => 'spec_desc',
            'is_default' => 'is_default',
            'market_able' => 'marketable',
        ),
        'output' => array(
        ),
    ),
    // products.update - 更新产品 
    'update' => array(
        'fields' => array(
            'products' => array(
                'product_id' => 'product_id',
                'goods_id' => 'goods_id',
                'sku_id' => 'bn',
                'product_code' => 'barcode',
                'product_name' => 'name',
                'product_price' => 'price',
                'mkt_price' => 'mktprice',
                'product_weight' => 'weight',
                'product_unit' => 'unit',
                'spec_info' => 'spec_info',
                'spec_desc' => 'spec_desc',
                'is_default' => 'is_default',
                'market_able' => 'marketable',
            ),
            'filter' => array(
                'product_id' => 'product_id',
                'goods_id' => 'goods_id',
            ),
        ),
        'output' => array(
        ),
    ),
    // products.delete - 删除产品 
    'delete' => array(
        'fields' => array(
            'goods_id' => 'goods_id',
        ),
        'output' => array(
        ),
    ),
);
