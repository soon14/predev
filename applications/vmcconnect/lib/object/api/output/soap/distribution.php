<?php

/**
 * Created by PhpStorm.
 * User: Ganxiaohui
 * Date: 2017/6/22
 * Time: 19:53
 */
class vmcconnect_object_api_output_def_distribution extends vmcconnect_object_api_output_def_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->pack_name = 'distribution';
    }


    // distribution.read.get - 查询配送方式
    public function read_get() {
        $func_args = func_get_args();
        $params = $func_args ? current($func_args) : null;
        return $params;
    }

}