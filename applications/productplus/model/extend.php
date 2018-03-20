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


class productplus_mdl_extend extends dbeav_model
{
    public $has_many = array(
        'images' => 'image_attach@productplus:contrast:product_id^target_id',
    );
    public $has_one = array();
    public $subSdf = array(
        'default' => array(
            'images' => array(
                'image_id',
            ),
        ) ,
        'delete' => array(
            'images' => array(
                '*',
            ),
        ),
    );

}
