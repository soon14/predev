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



class system_queue_message_mysql extends system_queue_message_abstract implements system_interface_queue_message{
    private $__id = null;
    private $__params = null;
    private $__worker = null;
    
    function __construct($queue_data){
        $this->__id = $queue_data['id'];
        $this->__params = $queue_data['params'];
        $this->__worker = $queue_data['worker'];
    }

    function get_params(){
        return $this->__params;
    }

    function get_worker(){
        return $this->__worker;
    }
    
    function get_id() {
        return $this->__id;
    }
}
