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


$db['images'] = array(
    'columns' => array(
        'image_id' => array(
            'type' => 'char(32)',
            'label' => '图片ID' ,
            'required' => true,
            'pkey' => true,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => '上传时间',
            'required' => true,
        )

    )
);