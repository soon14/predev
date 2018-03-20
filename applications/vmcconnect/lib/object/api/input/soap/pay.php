
<?php

/**
 * Created by PhpStorm.
 * User: Ganxiaohui
 * Date: 2017/6/22
 * Time: 19:43
 */
class vmcconnect_object_api_input_def_pay extends vmcconnect_object_api_input_def_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->pack_name = 'pay';
    }

    // pay.read.get - 查询支付方式
    public function read_get() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

}