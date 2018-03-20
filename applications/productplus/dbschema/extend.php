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


$db['extend'] = array(
    'columns' => array(
        'extend_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => 'ID' ,
        ) ,
        'product_id' => array(
            'type' => 'table:products@b2c',
            'required' => true,
            'label' => '货品ID' ,
        ) ,
        'title'=>array(
            'type'=>'varchar(255)',
            'label'=>'个性标题'
        ),
        'brief' => array(
            'type' => 'varchar(255)',
            'label' => '个性简介' ,
            'filtertype' => 'normal',
            'in_list' => true,
        ) ,
        'image_default' => array(
            'type' => 'char(32)',
            'label' => '默认相册图' ,
        ) ,
        // 'life_day'=>array(
        //     'type'=>'number',
        //     'label'=>'保质期(天)',
        // ),
        'has_desc'=>array(
            'type'=>'bool',
            'label'=>'是否启用特殊描述',
            'required'=>true,
            'default'=>'true'

        ),
        'description'=>array(
            'type'=>'longtext',
            'label'=>'货品特殊的描述内容'
        ),
        'enabled'=>array(
            'type'=>'bool',
            'required'=>true,
            'default'=>'false'
        )
    ) ,
    'engine' => 'innodb',
    'comment' => ('货品扩展表') ,
);
