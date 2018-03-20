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


$db['voucher'] = array(
    'columns' => array(
        'voucher_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'label' => '结算凭证号' ,
            'searchtype' => 'nequal',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'order_id' => array(
            'type' => 'table:orders@b2c',
            'required' => true,
            'label' => '订单号',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'delivery_id' => array(
            'type' => 'table:delivery@b2c',
            'required' => true,
            'label' => '发(退)货单号',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'shop_id' => array(
            'type' => 'table:shop',
            'required' => true,
            'label' => '微店铺ID',
            'filtertype' => 'yes',
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'shop_name' => array(
            'type' => 'varchar(50)',
            'label' => '微店铺名称' ,
            'filtertype' => 'yes',
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => ('单据创建时间') ,

            'filtertype' => 'yes',
            'orderby' => true,
            'in_list' => true,
        ) ,
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => ('最后更新时间') ,
            'filtertype' => 'yes',
            'orderby' => true,
            'in_list' => true,
        ) ,
        'status' => array(
            'label' => '状态' ,
            'type' => array(
                'ready' => '单据成功创建' ,
                'process' => '处理中' ,
                'succ' => '已被确认' ,
                'cancel' => '已取消' ,
            ) ,
            'default' => 'ready',
            'filtertype' => 'yes',
            'orderby' => true,
            'in_list' => true,
            'default_in_list' => true,
            'required' => true,
        ) ,
        'memo' => array(
            'type' => 'longtext',
            'label' => '备注' ,
            'label' => '备注' ,
        ) ,
        'total_subprice'=>array(
            'type' => 'money',
            'default' => 0,
            'required' => true,
            'label' => '应结算金额' ,
            'orderby' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'disabled' => array(
            'type' => 'bool',
            'default' => 'false',
            'label' => ('是否无效') ,
        ) ,
    ),
    'label' => ('结算凭证表') ,
);
