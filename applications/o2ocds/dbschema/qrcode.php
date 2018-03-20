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

$db['qrcode'] = array(
    'columns' => array(
        'qrcode_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ),
        'qrcode' => array(
            'type' => 'int(5)',
            'label' => '序号',
            'in_list' => true,
            'searchtype' => 'has',
        ),
        'prefix' => array(
            'type' => 'varchar(50)',
            'default' => '',
            'label' => '批次号',
            'in_list' => true,
            'searchtype' => 'has',
            'filtertype' => 'yes',
        ) ,
        'store_id' => array(
            'type' => 'table:store',
            'label' => '绑定店铺',
            'in_list' => true,
        ),
        'enterprise_id' => array(
            'type' => 'table:enterprise',
            'label' => '绑定企业',
            'in_list' => true,
        ),
        'status' => array(
            'type' => array(
                '0' => '未激活',
                '1' => '已激活',
            ),
            'default' => '0',
            'label' => '状态',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'normal',
        ),
        'remark' => array(
            'type' => 'longtext',
            'label' => ('备注') ,
            'width' => 50,
            'filtertype' => 'normal',
            'in_list' => true,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => '生成时间',
            'in_list'=> true,
            'default_in_list' => true,
            'orderby' => true,
            'filtertype' => 'yes',
        ),
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => '更新时间',
            'in_list'=> true,
            'default_in_list' => true,
            'orderby' => true,
            'filtertype' => 'yes',
        )
    )
);
