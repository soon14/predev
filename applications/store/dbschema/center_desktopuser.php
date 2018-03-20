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


$db['center_desktopuser'] = array(
    'columns' => array(
        'relation_id' => array(
            'type'     => 'mediumint(8)',
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment'  => ('关联表主键id'),
        ),
        'user_id' => array(
            'type'     => 'mediumint(8)',
            'required' => true,
            'default'  => '0',
            'comment'  => ('后台操作员id'),
        ),
        'center_id' => array(
            'type'     => 'table:center',
            'required' => true,
            'default'  => '0',
            'comment'  => ('收益台id'),
        ),
    ),
    'engine'  => 'innodb',
    'comment' => ('店铺操作员关联表'),
);
