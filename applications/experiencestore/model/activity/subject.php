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


class experiencestore_mdl_activity_subject extends dbeav_model
{
    public $defaultOrder = array(
        'sort DESC,id DESC',
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
