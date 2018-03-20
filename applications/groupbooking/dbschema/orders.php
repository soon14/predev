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
        'gb_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'default' => 0,
            'pkey' => true,
            'label' => ('拼团单号') ,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => 'custom',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'main_id' => array(
            'type' => 'bigint unsigned',
            'label' => '主订单ID',
            'default' => '0',
            'searchtype' => 'has',
            'filtertype' => 'custom',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'activity_id' => array(
            'type' => 'table:activity',
            'comment' => '拼团活动id',
            'required' => true,
            'label' => ('拼团名称') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'member_id' => array(
            'type' => 'table:members@b2c',
            'label' => ('会员用户名') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'order_id' => array(
            'type' => 'table:orders@b2c',
            'label' => '关联订单号',
            'default' => '0',
            'searchtype' => 'has',
            'filtertype' => 'custom',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'status' => array(
            'type' => array(
                '0' => ('待成团') ,
                '1' => ('已成团') ,
            ) ,
            'filtertype' => 'normal',
            'default' => '0',
            'required' => true,
            'in_list' => true,
            'label' => ('状态') ,
        ) ,
        'is_failure' => array(
            'type' => array(
                '0' => ('否') ,
                '1' => ('是') ,
            ) ,
            'filtertype' => 'normal',
            'default' => '0',
            'required' => true,
            'label' => ('是否失效') ,
            'comment' => ('是否失效') ,
            'in_list' => true,
        ) ,
        'order_total' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('团单金额') ,
            'filtertype' => 'number',
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        ) ,
        'is_refund' => array(
            'type' => array(
                0 => ('否') ,
                1 => ('是') ,
            ) ,
            'default' => '0',
            'required' => true,
            'label' => ('是否退款') ,
            'filtertype' => 'normal',
        ) ,
        'bill_id' => array(
            'type' => 'table:bills@ectools',
            'default' => '0',
            'label' => '账单流水号' ,
        ) ,
        'pay_status' => array(
            'type' => array(
                0 => ('未支付') ,
                1 => ('已支付') ,
                2 => ('已付款至到担保方') ,
                3 => ('部分付款') ,
                4 => ('部分退款') ,
                5 => ('全额退款') ,
            ) ,
            'default' => '0',
            'required' => true,
            'in_list' => true,
            'label' => ('付款状态') ,
            'filtertype' => 'normal',
        ) ,
        'payed' => array(
            'type' => 'money',
            'default' => '0',
            'label' => ('订单已支付金额') ,
        ) ,
        'pay_app' => array(
            'type' => 'varchar(100)',
            'label' => ('支付方式') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'dlytype_id' => array(
            'type' => 'table:dlytype@b2c',
            'label' => ('配送方式') ,
            'filtertype' => 'yes',
            'in_list' => false,
        ) ,
        'consignee_name' => array(
            'type' => 'varchar(50)',
            'label' => ('收货人') ,
            'sdfpath' => 'consignee/name',
            'searchtype' => 'head',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'consignee_area' => array(
            'type' => 'region',
            'sdfpath' => 'consignee/area',
            'label' => ('收货地区') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'consignee_address' => array(
            'type' => 'text',
            'sdfpath' => 'consignee/addr',
            'searchtype' => 'has',
            'width' => 180,
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
            'label' => ('收货地址') ,
        ) ,
        'consignee_zip' => array(
            'type' => 'varchar(20)',
            'sdfpath' => 'consignee/zip',
            'label' => ('收货地邮编') ,
        ) ,
        'consignee_tel' => array(
            'type' => 'varchar(50)',
            'sdfpath' => 'consignee/tel',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
            'label' => ('收货人固话') ,
        ) ,
        'consignee_email' => array(
            'type' => 'varchar(200)',
            'sdfpath' => 'consignee/email',
            'label' => ('收货人Email') ,
            'in_list' => true,
        ) ,
        'consignee_mobile' => array(
            'searchtype' => 'has',
            'type' => 'varchar(50)',
            'sdfpath' => 'consignee/mobile',
            'in_list' => true,
            'default_in_list' => true,
            'label' => ('收货人手机') ,
        ) ,
        'cost_freight' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('配送费用') ,
            'filtertype' => 'number',
            'in_list' => true,
        ) ,
        'invoice_title' => array(
            'type' => 'varchar(255)',
            'label' => ('发票抬头') ,
            'default' => '个人',
        ) ,
        /*商品信息*/
        'product_id' => array(
            'type' => 'table:products@b2c',
            'required' => true,
            'default' => 0,
            'comment' => ('货品ID') ,
        ) ,
        'goods_id' => array(
            'type' => 'table:goods@b2c',
            'required' => true,
            'default' => 0,
            'comment' => ('商品ID') ,
        ) ,
        'bn' => array(
            'type' => 'varchar(40)',
            'comment' => ('明细商品货号') ,
        ) ,
        'barcode' => array(
            'type' => 'varchar(128)',
            'label' => ('条码') ,
        ) ,
        'name' => array(
            'type' => 'varchar(200)',
            'is_title' => true,
            'comment' => ('明细商品的名称') ,
        ) ,
        'spec_info' => array(
            'type' => 'varchar(200)',

            'comment' => ('商品规格描述') ,
        ) ,
        'image_id' => array (
            'type' => 'table:image@image',
            'required' => true,
            'default' => 0,
            'comment' => '图片ID',
        ),
        'price' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,

            'comment' => ('销售价') ,
        ) ,
        'buy_price' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,

            'comment' => ('拼团价') ,
        ) ,
        'nums' => array(
            'type' => 'float',
            'default' => 1,
            'required' => true,

            'comment' => ('明细商品购买数量') ,
        ) ,
        'amount' => array(
            'type' => 'money',

            'comment' => ('明细商品总额(拼团价x数量)') ,
        ) ,
        'weight' => array(
            'type' => 'number',

            'comment' => ('明细商品重量') ,
        ) ,
        'memo' => array(
            'type' => 'longtext',
            'label' => ('订单创建时附言') ,
        ) ,
        'ip' => array(
            'type' => 'varchar(15)',
            'label' => ('下单IP地址') ,
            'in_list' => true,
        ) ,
        'createtime' => array(
            'label' => ('创建时间') ,
            'type' => 'time',
            'required' => true,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'last_modified' => array(
            'label' => ('最后更新时间') ,
            'type' => 'last_modify',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
    ),
    'index' => array(
        'ind_item_bn' => array(
            'columns' => array(
                0 => 'bn',
            ) ,
            'type' => 'hash',
        ) ,

    ) ,
);

?>