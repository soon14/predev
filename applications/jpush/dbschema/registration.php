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


$db['registration'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'varchar(50)',
            'required' => true,
            'pkey' => true,
            'label' => '客户端ID' ,
            'filtertype' => 'normal',
            'searchtype' => 'has',
            'in_list'=>true,
            'default_in_list' => true,
        ) ,
        'alias'=>array(
            'type'=>'varchar(50)',
            'label'=>'别名',
            'searchtype' => 'has',
            'in_list'=>true,
            'default_in_list' => true,
        ),
        'platform'=>array(
            'type'=>array(
                'ios'=>'苹果iOS',
                'android'=>'安卓Android'
            ),
            'label'=>'客户端操作系统',
            'in_list'=>true,
            'default_in_list' => true,
        ),
        'platform_version'=>array(
            'type'=>'varchar(20)',
            'label'=>'客户端操作系统版本',
            'in_list'=>true,
            'default_in_list' => true,
        ),
        'device'=>array(
            'type'=>'varchar(100)',
            'label'=>'设备型号',
            'in_list'=>true,
            'default_in_list' => true,
        ),
        'device_version'=>array(
            'type'=>'varchar(20)',
            'label'=>'设备版本',
            'in_list'=>true,
            'default_in_list' => true,
        ),
        'member_id'=>array(
            'type'=>'table:members@b2c',
            'label'=>'会员',
            'in_list'=>true,
            'default_in_list' => true,
        ),
        'createtime'=>array(
            'type'=>'time',
            'label'=>'首次绑定时间',
            'filtertype' => 'normal',
            'in_list'=>true,
            'default_in_list' => true,
        ),
        'last_modify'=>array(
            'type'=>'last_modify',
            'label'=>'更新时间',
            'filtertype' => 'normal',
            'in_list'=>true,
            'default_in_list' => true,
        ),
    ) ,
    'engine' => 'innodb',
    'comment' => ('客户端registration_id绑定表') ,
);
