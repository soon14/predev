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


class jpush_mdl_registration extends dbeav_model
{
    var $has_tag = true;
    var $defaultOrder = array('createtime','DESC');
    public function __construct($app)
    {
        parent::__construct($app);

    }
    
    public function modifier_alias($col,$row)
    {
        $registration_id = $row['id'];

        $_return = <<<HTML
            <input class='form-control edit-col input-sm input-xsmall' name="alias" type='text' data-pkey='$registration_id' value='$col'>
HTML;

        return $_return;
    }

}
