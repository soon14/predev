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
class digitalmarketing_mdl_partin extends dbeav_model{
    public function modifier_member_id($row)
    {
        if ($row === 0 || $row == '0'){
            return ('非会员顾客');
        }
        else{
            return vmc::singleton('b2c_user_object')->get_member_name(null,$row);
        }
    }
}