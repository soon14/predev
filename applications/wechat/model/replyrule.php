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


class wechat_mdl_replyrule extends dbeav_model
{
    public $has_one = array(
        'media' => 'media:replace',
    );

    public $subSdf = array(
        'default' => array(
            'media' => array(
                '*',
            ),
        ),
    );
    public function modifier_keywords($col)
    {
        if (substr($col, -1) == ',') {
            return substr($col, 0, -1);
        }

        return $col;
    }
}
