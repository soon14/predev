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

class vmcconnect_api_router extends vmcconnect_api_base {
    
    public function __construct(&$app) {
        parent::__construct($app);
        
    }
    
    /**
     * 构造方法
     * @param object $app
     */
    public function json() {
        if(!$this->get_api_status()) return false;
        $this->_out_type = 'json';
        $this->init()->run()->out();
    }

    /**
     * 统一 api入口
     */
    public function xml() {
        if(!$this->get_api_status()) return false;
        $this->_out_type = 'xml';
        $this->init()->run()->out();
    }
    
    public function hook() {
        myfun::vard('POST VALUES', $_POST);
    }
    
    public function test() {
        vmc::singleton('vmcconnect_object_test_api')->test();
    }

}
