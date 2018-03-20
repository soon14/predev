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
class commission_mdl_orderlog extends dbeav_model
{
    public $defaultOrder = array('createtime','DESC');
    public $has_many = array(
        'items' => 'orderlog_items:append',
        'achieve' => 'orderlog_achieve:append',
    );

    public function modifier_from_id($row)
    {
        return vmc::singleton('b2c_user_object')->get_member_name(null,$row);

    }
}