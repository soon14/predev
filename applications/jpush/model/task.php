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


class jpush_mdl_task extends dbeav_model
{
    var $has_tag = true;
    var $defaultOrder = array('createtime','DESC');
    public function __construct($app)
    {
        parent::__construct($app);

    }

}
