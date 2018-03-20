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


$db['orderlog'] = array(
    'columns' => array(
        'orderlog_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => 'ID',
            'pkey' => true,
            'extra' => 'auto_increment',
            'in_list' => true,
        ),
        'order_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'label' => '订单ID',
            'comment' => '订单ID',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'from_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '订单来源ID',
            'comment' => '订单来源ID',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'order_fund' => array(
            'type' => 'money',
            'required' => true,
            'label' => '佣金',
            'comment' => '产生分佣金额',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'settle_status' => array(
            'type' =>
                array(
                    '0' => ('未结算'),
                    '1' => ('已结算'),
                    '2' => ('买家已退款'),
                ),
            'required' => true,
            'default' => '0',
            'label' => '分佣结算状态',
            'comment' => '结算状态',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'createtime' => array(
            'type' => 'time',
            'required' => true,
            'label' => '生成时间',
            'comment' => '生成时间',
            'in_list' => true,
            'default_in_list' => true,
        ),
    ),
    'index' => array(
        'ind_order_id' => array(
            'columns' =>
                array(
                    0 => 'order_id',
                ),
        )
    ),
    'comment' => '订单分佣记录表',
);
