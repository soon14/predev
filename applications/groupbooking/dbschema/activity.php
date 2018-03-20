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

$db['activity'] = array(
    'columns' => array(
        'activity_id' => array(
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => '活动id',
        ),
        'name' => array(
            'type' => 'varchar(50)',
            'required' => true,
            'label' => '活动名称',
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
        ),
        'start_time' => array(
            'type' => 'time',
            'required' => true,
            'label' => '开始时间',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ),
        'end_time' => array(
            'type' => 'time',
            'required' => true,
            'label' => '结束时间',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ),
        'people_number' => array(
            'type' => 'number',
            'required' => true,
            'label' => '参团人数',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ),
        'member_lv_ids' => array(
            'type' => 'varchar(255)',
            'default' => '',
            'required' => true,
            'label' => ('会员等级'),
            'comment' => ('会员级别集合'),
        ),
        'goods_id' => array(
            'type' => 'table:goods@b2c',
            'required' => true,
            'label' => ('参与商品'),
        ),
        'product_id' => array(
            'type' => 'table:products@b2c',
            'required' => true,
            'label' => ('默认货品'),
        ),
        'conditions' => array(
            'type' => 'serialize',
            'default' => '',
            'required' => true,
            'label' => ('规则条件'),
        ),
        'status' => array(
            'label' => '状态' ,
            'type' => array(
                'process' => '进行中' ,
                'cancel' => '已结束' ,
            ) ,
            'default' => 'process',
        ),
        'w_order' => array(
            'type' => 'number',
            'default' => 0,
            'required' => true,
            'label' => ('权重') ,
            'filtertype' => 'normal',
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'required' => true,
            'label' => '创建时间',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ),

    )
);