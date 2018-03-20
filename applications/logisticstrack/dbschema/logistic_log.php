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


$db['logistic_log'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ) ,
        'dtline' => array(
            'type' => 'time',
            'required' => true,
            'label' => '最后同步时间' , //最后从中心拉取时间
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',

        ) ,
        'delivery_id' => array(
            'type' => 'varchar(20)',
            'required' => true,
            'label' => '(发\退)货单号' ,
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
            'filtertype' => 'has',
        ) ,
        'pulltimes' => array(
            'type' => 'number',
            'label' => '同步次数' , // 从中心拉取次数
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'dly_corp' => array(
            'type' => 'varchar(200)',
            'default' => '',
            'label' => '物流公司' ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'logistic_no' => array(
            'type' => 'varchar(64)',
            'default' => '',
            'label' => '物流单号' ,
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
            'filtertype' => 'has',
        ) ,
        'logistic_log' => array(
            'type' => 'text',
            'default' => '',
            'comment' => '跟踪记录' ,
        ) ,
    ) ,
    'comment' => '物流状态记录表' ,
    'index' => array(
        'ind_delivery_id' => array( // 索引名
            'columns' => array( // 索引列
                0 => 'delivery_id',
            ) ,
            'prefix' => 'UNIQUE', // 索引类型 fulltext unique
            'type' => ''
            // 索引算法 BTREE HASH RTREE

        ) ,
    ) ,
    'comment' => '物流跟踪表' ,
);
