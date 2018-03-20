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


$db['relation'] = array(
    'columns' => array(
        'user_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '用户ID',
        ),
        'relation_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => '关注的用户ID',
        ),
        'bind_relation_time' => array(
            'type' => 'time',
            'label' => '关注时间' ,
        ) ,
    ) ,
    'index' => array(
        'relation' => array(
            'columns' => array(
                'user_id',
                'relation_id',
            ) ,
            'prefix' => 'UNIQUE',
        ),
    ) ,
    'engine' => 'innodb',
    'comment' => ('用户关系表') ,
);
