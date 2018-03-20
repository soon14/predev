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


$db['voucher_items'] = array(
    'columns' => array(
        'item_id' => array(
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => ('序号') ,
        ) ,
        'voucher_id' => array(
            'type' => 'table:voucher',
            'required' => true,
            'default' => 0,
            'comment' => ('结算凭证号') ,
        ) ,
        'delivery_item_id' => array(
            'type' => 'table:delivery_items@b2c',
            'default' => 0,
            'comment' => ('原始货单明细编号') ,
        ) ,
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
            'comment' => ('SKU货号') ,
        ) ,
        'name' => array(
            'type' => 'varchar(200)',
            'comment' => ('明细商品的名称') ,
        ) ,
        'spec_info' => array(
            'type' => 'varchar(200)',
            'comment' => ('商品规格描述') ,
        ) ,
        'image_id' => array(
            'type' => 'table:image@image',
            'required' => true,
            'default' => 0,
            'comment' => '图片ID',
        ) ,
        's_num' => array(
            'type' => 'float',
            'default' => 0,
            'required' => true,
            'comment' => '结算数量' ,
        ) ,
        's_price' => array(
            'type' => 'money',
            'default' => 0,
            'required' => true,
            'comment' => '结算单价' ,
        ) ,
        's_subprice' => array(
            'type' => 'money',
            'default' => 0,
            'required' => true,
            'comment' => '结算小计' ,
        ) ,
    ) ,
    'index' => array(
      'ind_voucher' => array(
        'columns' => array(
          0 => 'voucher_id',
        ),
      ),
      'ind_product_bn' => array(
        'columns' => array(
          0 => 'bn',
        ),
      ),
      'ind_product' => array(
        'columns' => array(
          0 => 'product_id',
        ),
      ),
      'ind_goods' => array(
        'columns' => array(
          0 => 'goods_id',
        ),
      ),
    ),
    'comment' => ('结算凭证明细表') ,
);
