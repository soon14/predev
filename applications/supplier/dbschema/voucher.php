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
        'delivery_id' => array(
            'type' => 'table:delivery@b2c',
            'required' => true,
            'label' => '发(退)货单号',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'supplier_id' => array(
            'type' => 'table:supplier',
            'required' => true,
            'label' => '供应商',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'supplier_bn' => array(
            'type' => 'varchar(20)',
            'label' => '供应商编号' ,
            'filtertype' => 'yes',
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'comment' => ('单据创建时间') ,
            'label' => ('单据创建时间') ,
            'filtertype' => 'yes',
            'orderby' => true,
            'in_list' => true,
        ) ,
        'last_modify' => array(
            'type' => 'last_modify',
            'comment' => ('最后更新时间') ,
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
            'comment' => '备注' ,
        ) ,
        'disabled' => array(
            'type' => 'bool',
            'default' => 'false',
            'comment' => ('是否无效') ,
        ) ,
    ),
    'comment' => ('结算凭证表') ,
);
