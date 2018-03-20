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

class vmcconnect_object_hook_output_soap_clue extends vmcconnect_object_hook_output_soap_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->_name = 'stock';
    }

    public function create() {

        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;

        $member_clue_schema = app::get('b2c')->model('member_clue')->get_schema();

        $sex_type = $member_clue_schema['columns']['sex']['type'];
        $clue_type = $member_clue_schema['columns']['clue_type']['type'];
        $car_type = $member_clue_schema['columns']['car_type']['type'];

        $data = ($params && isset($params['data'])) ? $params['data'] : array();
        $data['type_name'] = $clue_type[$data['clue_type']];
        $data['car_type_name'] = $car_type[$data['car_type']];
        $data['gender'] = $sex_type[$data['sex']];

        return $this->get_params($data, $fields_map);
    }

}
