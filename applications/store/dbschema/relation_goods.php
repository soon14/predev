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


$db['relation_goods'] = array(
    'columns' => array(
        'goods_id'       => array(
            'pkey' => true,
            'type'    => 'number',
            'required' => true,
            'default' => '0',
            'comment' => ('商品id'),
        ),
        'store_enable' => array(
            'type'=> array(
                '0' =>'否',
                '1' =>'是'
            ),
            'required' => true,
            'default'  => '0',
            'label' => '是否门店上架',
            'comment'  => ('是否门店上架'),
            'in_list' => true,
        ),

        'store_id' => array(
            'type'    => 'table:store',
            'required' => true,
            'default' => '0',
            'label' => '店铺名称',
            'comment'  => ('店铺id'),
            'in_list' => true,
        )
    ),
    'version' => '1.0',
    'engine'  => 'innodb',
    'comment' => ('商品店铺关联表'),
);
