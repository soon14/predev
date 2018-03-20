<?php

$setting = array(
    'default_profit' => array(
        'type' => 'text',
        'default' => '0.1',
        'required' => true,
        'desc' => '微店单品默认分润设置',
        'helpinfo' => '0.1代表10%,1代表1元。'
    ),
    'profit_by_self' => array(
        'type' => 'select',
        'options' => array(
            'true' => '是',
            'false' => '否',
        ),
        'default' => 'true',
        'desc' => ('店主通过自己的链接购买是否分润？'),
    ) ,
    'profit_by_buyprice' => array(
        'type' => 'select',
        'options' => array(
            'true' => '是',
            'false' => '否',
        ),
        'default' => 'true',
        'desc' => ('分润是否基于商品成交价？否,则按照销售价分润'),
    ) ,
    // 'profit_share_orderpromotion' => array(
    //     'type' => 'select',
    //     'options' => array(
    //         'true' => '是',
    //         'false' => '否',
    //     ),
    //     'default' => 'true',
    //     'desc' => ('单品分润前是否均摊订单促销金额？'),
    // ) ,
    // 'vshop_threshold_buy' => array(
    //     'type' => 'text',
    //     'default' => '500',
    //     'required' => true,
    //     'desc' => '店主入住最低消费门槛',
    // ) ,
    // 'vshop_threshold_product' => array(
    //     'type' => 'text',
    //     'default' => '',
    //     'desc' => '店主入住必须消费商品sku编号',
    //     'helpinfo' => '多个sku逗号分割'
    // ) ,
);
