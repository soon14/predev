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


class vmcconnect_mdl_apilogs extends dbeav_model {
    
    public $defaultOrder = array(
        'log_id DESC',
    );

    public function __construct($app) {
        parent::__construct($app);
        $this->use_meta();
    }
    
    public function modifier_act_res($row)
    {
        return $row ? '<span class="text-success">成功</span>' : '<span class="text-danger">失败</span>';
    }

    public function modifier_log_date($row) {
        return date('Y-m-d H:i:s', $row);
    }
    
    public function modifier_log_ip($row) {
        return long2ip($row);
    }
}
