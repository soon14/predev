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


$db['statement'] = array(
    'columns' => array(
        'statement_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'default' => 0,
            'pkey' => true,
            'label' => '结算单流水号' ,
            'searchtype' => 'nequal',
            'in_list' => true,
            'default_in_list' => true,
            'is_title' => true,
        ) ,
        'money' => array(
            'type' => 'money',
            'label' => '应结算金额' ,
            'default' => '0',
            'required' => true,
            'filtertype' => 'yes',
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
        ) ,
        'op_id' => array(
            'type' => 'number', //'table:users@desktop',
            'label' => '相关操作员' ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'supplier_id' => array(
            'type' => 'table:supplier',
            'label' => '供应商' ,
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
        'status' => array(
            'type' => array(
                'process' => '处理中' ,
                'succ' => '支付成功',
            ) ,
            'default' => 'process',
            'orderby' => true,
            'required' => true,
            'label' => '支付状态' ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'payee' => array(
            'type' => 'varchar(50)',
            'label' => '收款人' ,
            'filtertype' => 'yes',
            'searchtype' => 'has',
            'in_list' => true,
        ) ,
        'payee_account' => array(
            'type' => 'varchar(50)',
            'label' => '收款账户' ,
            'filtertype' => 'yes',
            'searchtype' => 'has',
            'in_list' => true,
        ) ,
        'payee_bank' => array(
            'type' => 'varchar(100)',
            'label' => '收款银行' ,
            'filtertype' => 'yes',
            'in_list' => true,
        ) ,
        'payer' => array(
            'type' => 'varchar(50)',
            'label' => '付款人' ,
            'filtertype' => 'yes',
            'in_list' => true,
        ) ,
        'payer_account' => array(
            'type' => 'varchar(50)',
            'label' => '付款账户' ,
            'filtertype' => 'yes',
            'in_list' => true,
        ) ,
        'payer_bank' => array(
            'type' => 'varchar(100)',
            'label' => '付款银行' ,
            'filtertype' => 'yes',
            'in_list' => true,
        ) ,
        'pay_fee' => array(
            'type' => 'money',
            'label' => '支付手续费' ,
        ) ,
        'out_trade_no' => array(
            'type' => 'varchar(50)',
            'label' => '支付平台流水号' ,
            'searchtype' => 'nequal',
            'in_list' => true,
        ) ,
        'memo' => array(
            'type' => 'longtext',
            'label' => '备注' ,
            'searchtype' => 'has',
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => '结算单创建时间' ,
            'filtertype' => 'yes',
            'in_list' => true,
            'orderby' => true,
        ) ,
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => '最后更新时间' ,
            'filtertype' => 'yes',
            'orderby' => true,
            'in_list' => true,
        ) ,
    ) ,
    'index' => array(
      'ind_supplier' => array(
        'columns' => array(
          0 => 'supplier_id',
        ),
      ),
    ),
    'comment' => '供应商结算单表' ,
);
