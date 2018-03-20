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


class experiencestore_mdl_store extends dbeav_model
{
    public $defaultOrder = array(
        'sort ASC,id DESC',
    );
    public $has_many = array(
        'images' => 'store_image:contrast:id^target_id'
    );
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
    public function __construct($app)
    {
        parent::__construct($app);

    }
    /**
     * 删除前
     */
    public function pre_recycle($rows)
    {
        $this->recycle_msg = '删除成功!';
        //todo 判断
        return true;
    }
}
