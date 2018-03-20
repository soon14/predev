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


$db['orderlog_items'] = array(
    'columns' => array(
        'items_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'ID',
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'orderlog_id' => array(
            'type' => 'table:orderlog',
            'required' => true,
            'label' => '订单ID',
            'comment' => '订单ID',
        ),
        'product_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '产品ID',
            'comment' => '产品ID',
        ),
        'buy_price' => array(
            'type' => 'number',
            'required' => true,
            'label' => '成交价',
            'comment' => '成交价',
        ),
        'product_fund' => array(
            'type' => 'money',
            'required' => true,
            'label' => '单品分佣金额',
            'comment' => '单品分佣金额',
        ),
        'commission' => array(
            'type' => 'serialize',
            'required' => true,
            'label' => '佣金方式',
            'comment' => '佣金方式',
        ),
        'commission_items' => array(
            'type' => 'serialize',
            'required' => true,
            'label' => '详细信息',
            'comment' => '佣金详情',
        ),
        'nums' => array(
            'type' => 'number',
            'required' => true,
            'label' => '产品数量',
            'comment' => '产品数量',
        )
    ),
    'index' => array(
        'ind_orderlog_id' => array(
            'columns' =>
                array(
                    0 => 'orderlog_id',
                ),
        )
    ),
    'comment' => '订单分佣明细表',

);
