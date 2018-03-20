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

/**
 *  getConf 默认值.
 */
$setting = array(
    'self'=>array(
        'type'=>'select',
        'desc'=>'组合套餐是否能够自加减数量',
        'default' => 'false',
        'options' => array(
                'true' => '是',
                'false' => '否',
        ),
        'helpinfo'=>'如果选择否，则套餐商品只能随着主商品增减而增减'
    )
);
