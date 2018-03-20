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
            'label' => '预售活动名称',
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
        ),
        'deposit_starttime' => array(
            'type' => 'time',
            'required' => true,
            'label' => '订金支付开始时间',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ),
        'deposit_endtime' => array(
            'type' => 'time',
            'required' => true,
            'label' => '订金支付结束时间',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ),
        'balance_starttime' => array(
            'type' => 'time',
            'required' => true,
            'label' => '尾款支付开始时间',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ),
        'balance_endtime' => array(
            'type' => 'time',
            'required' => true,
            'label' => '尾款支付结束时间',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ),
        'send_time' => array(
            'type' => 'time',
            'required' => true,
            'label' => '预计发货时间',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ),
        'participate_num' => array(
            'type' => 'number',
            'label' => '参加人数',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'order_num' => array(
            'type' => 'number',
            'label' => '预售单量',
            'in_list' => true,
            'default_in_list' => true,
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
        'is_refund' => array(
            'label' => '定金是否退还' ,
            'type' => 'bool',
            'default' => 'true',
        ),
        'status' => array(
            'label' => '状态' ,
            'type' => array(
                'process' => '进行中' ,
                'cancel' => '已结束' ,
            ) ,
            'default' => 'process',
        ),
        'description' => array(
            'type' => 'text',
            'label' => ('活动说明') ,
        ) ,
        'warm_prompt' => array(
            'type' => 'text',
            'label' => ('商详页温馨提示') ,
        ) ,
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
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => ('更新时间') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'orderby' => true,
        ) ,
    )
);