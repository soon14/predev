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

class helloworld_mdl_hello extends dbeav_model
{
    public $defaultOrder = array(
        'col1 ASC,col2 ASC',
    );
    public function __construct($app)
    {
        parent::__construct($app);
        //$this->use_meta();

    }
    

}
