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


$db['orderlog_achieve'] = array(
    'columns' => array(
        'achieve_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'default' => 0,
            'pkey' => true,
            'label' => '结算凭证流水号',
            'searchtype' => 'nequal',
            'in_list' => true,
            'default_in_list' => true,
            'is_title' => true,
        ),
        'order_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'label' => '订单号',
            'comment' => '订单号',
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
        ),
        'delivery_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'label' => '发货单',
            'comment' => '发货单',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'orderlog_id' => array(
            'type' => 'table:orderlog',
            'required' => true,
            'label' => '订单ID',
            'comment' => '订单ID',
        ),
        'relation_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '分佣者',
            'comment' => '分佣者关联的id号',
            'searchtype' => 'has',
        ),
        'type' => array(
            'type' => array(
                'enterprise' => '企业',
                'store' => '店铺',
            ),
            'label' => '属性',
            'comment' => '分佣账号类型',
            'required' => true,
            'default' =>'enterprise',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'status' => array(
            'label' => '状态' ,
            'type' => array(
                'ready' => '单据成功创建' ,
                'process' => '处理中' ,
                'succ' => '已被确认' ,
                'cancel' => '已取消' ,
            ) ,
            'default' => 'ready',
            'in_list' => true,
            'default_in_list' => true,
            'required' => true,
            'filtertype' => 'yes',
            'orderby' => true,
        ),
        'achieve_fund' => array(
            'type' => 'money',
            'required' => true,
            'label' => '佣金',
            'comment' => '获取的佣金',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'orderby' => true,
        ),
        'createtime' => array(
            'type' => 'time',
            'label' => '生成时间',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'orderby' => true,
        ),
        'settlement_time' => array(
            'type' => 'time',
            'label' => '结算时间',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'orderby' => true,
        ),
        'memo' => array(
            'type' => 'longtext',
            'label' => '备注' ,
            'comment' => '备注' ,
        ) ,
    ),
    'comment' => '分佣流向记录表',
);
