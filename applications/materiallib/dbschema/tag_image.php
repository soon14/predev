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


$db['tag_image'] = array(
    'columns' => array(
        'tag_id' => array(
            'type' => 'bigint unsigned',
            'label' => '标签ID',
            'required' => true,
            'pkey' => true,
        ) ,
        'image_id' => array(
            'type' => 'char(32)',
            'label' => '图片ID' ,
            'required' => true,
            'pkey' => true,
        ) ,
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => ('更新时间') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'orderby' => true,
        ) ,
    ),
    'index' => array(
        'tag_image_unique' => array(
            'columns' => array(
                0 => 'tag_id',
                1 => 'image_id',
            ),
            'prefix' => 'unique',
        ),
    )
);