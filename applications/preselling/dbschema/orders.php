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

$db['orders'] = array(
    'columns' => array(
        'presell_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'default' => 0,
            'pkey' => true,
            'label' => ('预售单号'),
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => 'custom',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'activity_id' => array(
            'type' => 'table:activity',
            'comment' => '预售活动id',
            'required' => true,
            'label' => ('预售名称'),
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'member_id' => array(
            'type' => 'table:members@b2c',
            'label' => ('会员用户名'),
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'order_id' => array(
            'type' => 'table:orders@b2c',
            'label' => '关联订单号',
            'default' => '0',
            'searchtype' => 'has',
            'filtertype' => 'custom',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'order_total' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('预售单金额'),
            'filtertype' => 'number',
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        ),
        'deposit_price' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('预售单定金'),
            'filtertype' => 'number',
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        ),
        'deposit_deduction' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('预售定金可抵扣金额'),
        ),
        'deposit_bill_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'default' => 0,
            'label' => '订金支付流水号',
            'searchtype' => 'nequal',
            'in_list' => true,
            'default_in_list' => true,
            'is_title' => true,
        ),
        'deposit_refund_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'default' => 0,
            'label' => '订金退款支付流水号',
        ),
        'balance_payment' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('预售单尾款'),
            'filtertype' => 'number',
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        ),
        'balance_bill_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'default' => 0,
            'label' => '尾款支付流水号',
            'searchtype' => 'nequal',
            'in_list' => true,
            'default_in_list' => true,
            'is_title' => true,
        ),
        'balance_refund_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'default' => 0,
            'label' => '尾款退款支付流水号',
        ),
        'deposit_pay_status' => array(
            'type' => array(
                0 => ('未付款'),
                1 => ('已付定金'),
                2 => ('定金退款中'),
                3 => ('定金已退款'),
            ),
            'default' => '0',
            'required' => true,
            'label' => ('定金支付状态'),
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'normal',
        ),
        'balance_pay_status' => array(
            'type' => array(
                0 => ('未付款'),
                1 => ('已付尾款'),
                2 => ('尾款退款中'),
                3 => ('尾款已退款'),
            ),
            'default' => '0',
            'required' => true,
            'label' => ('尾款支付状态'),
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'normal',
        ),
        'status' => array(
            'type' => array(
                0 => ('待付订金'),
                1 => ('已付订金'),
                2 => ('预售成功'),
                3 => ('预售失败'),
                //4 => ('待补尾款') ,//已付定金，在活动待付尾款直接内
            ),
            'default' => '0',
            'required' => true,
            'in_list' => true,
            'label' => ('预售单状态'),
            'filtertype' => 'normal',
        ),
        'balance_starttime' => array(
            'type' => 'time',
            'required' => true,
            'label' => '尾款支付开始时间',
            'in_list' => true,
            'filtertype' => 'yes',
        ),
        'balance_endtime' => array(
            'type' => 'time',
            'required' => true,
            'label' => '尾款支付结束时间',
            'in_list' => true,
            'filtertype' => 'yes',
        ),
        'pay_app' => array(
            'type' => 'varchar(100)',
            'label' => ('支付方式'),
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'dlytype_id' => array(
            'type' => 'table:dlytype@b2c',
            'label' => ('配送方式'),
            'filtertype' => 'yes',
            'in_list' => false,
        ),
        'consignee_name' => array(
            'type' => 'varchar(50)',
            'label' => ('收货人'),
            'sdfpath' => 'consignee/name',
            'searchtype' => 'head',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'consignee_area' => array(
            'type' => 'region',
            'sdfpath' => 'consignee/area',
            'label' => ('收货地区'),
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'consignee_address' => array(
            'type' => 'text',
            'sdfpath' => 'consignee/addr',
            'searchtype' => 'has',
            'width' => 180,
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
            'label' => ('收货地址'),
        ),
        'consignee_zip' => array(
            'type' => 'varchar(20)',
            'sdfpath' => 'consignee/zip',
            'label' => ('收货地邮编'),
        ),
        'consignee_tel' => array(
            'type' => 'varchar(50)',
            'sdfpath' => 'consignee/tel',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
            'label' => ('收货人固话'),
        ),
        'consignee_email' => array(
            'type' => 'varchar(200)',
            'sdfpath' => 'consignee/email',
            'label' => ('收货人Email'),
            'in_list' => true,
        ),
        'consignee_mobile' => array(
            'searchtype' => 'has',
            'type' => 'varchar(50)',
            'sdfpath' => 'consignee/mobile',
            'in_list' => true,
            'default_in_list' => true,
            'label' => ('收货人手机'),
        ),
        'cost_freight' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('配送费用'),
            'filtertype' => 'number',
            'in_list' => true,
        ),
        'invoice_title' => array(
            'type' => 'varchar(255)',
            'label' => ('发票抬头'),
            'default' => '个人',
        ),
        /*商品信息*/
        'product_id' => array(
            'type' => 'table:products@b2c',
            'required' => true,
            'default' => 0,
            'comment' => ('货品ID'),
        ),
        'goods_id' => array(
            'type' => 'table:goods@b2c',
            'required' => true,
            'default' => 0,
            'comment' => ('商品ID'),
        ),
        'bn' => array(
            'type' => 'varchar(40)',
            'comment' => ('明细商品货号'),
        ),
        'barcode' => array(
            'type' => 'varchar(128)',
            'label' => ('条码'),
        ),
        'name' => array(
            'type' => 'varchar(200)',
            'is_title' => true,
            'comment' => ('明细商品的名称'),
        ),
        'spec_info' => array(
            'type' => 'varchar(200)',

            'comment' => ('商品规格描述'),
        ),
        'image_id' => array(
            'type' => 'table:image@image',
            'required' => true,
            'default' => 0,
            'comment' => '图片ID',
        ),
        'price' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,

            'comment' => ('销售价'),
        ),
        'buy_price' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,

            'comment' => ('预售价'),
        ),
        'nums' => array(
            'type' => 'float',
            'default' => 1,
            'required' => true,

            'comment' => ('明细商品购买数量'),
        ),
        'amount' => array(
            'type' => 'money',

            'comment' => ('明细商品总额(预售价x数量)'),
        ),
        'weight' => array(
            'type' => 'number',

            'comment' => ('明细商品重量'),
        ),
        'memo' => array(
            'type' => 'longtext',
            'label' => ('订单创建时附言'),
        ),
        'remarks' => array(
            'type' => 'longtext',
            'label' => ('订单处理人员备注'),
        ),
        'ip' => array(
            'type' => 'varchar(15)',
            'label' => ('下单IP地址'),
            'in_list' => true,
        ),
        'createtime' => array(
            'label' => ('创建时间'),
            'type' => 'time',
            'required' => true,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'last_modified' => array(
            'label' => ('最后更新时间'),
            'type' => 'last_modify',
            'in_list' => true,
            'default_in_list' => true,
        ),
    ),
    'index' => array(
        'ind_item_bn' => array(
            'columns' => array(
                0 => 'bn',
            ),
            'type' => 'hash',
        ),

    ),
);

?>