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

class vmcconnect_object_hook_output_jd_category extends vmcconnect_object_hook_output_jd_base {

    public function __construct($app) {
        parent::__construct($app);
        $this->_name = 'category';
    }

    public function save() {
        
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;
        
        $data = ($params && isset($params['data'])) ? $params['data'] : array();
        $res = array();
        $res['visible'] = ($data && isset($data['addon']['visible'])) ? ($data['addon']['visible'] == 'true' ? true : false)  : true;
        $res['cat_id'] = ($data && isset($data['cat_id'])) ? $data['cat_id'] : null;
        $res['cat_name'] = ($data && isset($data['cat_name'])) ? $data['cat_name'] : null;
        $res['p_order'] = ($data && isset($data['p_order'])) ? $data['p_order'] : null;
        $res['parent_id'] = ($data && isset($data['parent_id'])) ? $data['parent_id'] : null;
        
        return $this->get_params($res, $fields_map);
    }

    public function remove() {
        $func_args = func_get_args();
        $params = ($func_args && isset($func_args[0])) ? $func_args[0] : null;
        $fields_map = ($func_args && isset($func_args[1])) ? $func_args[1] : null;
        return $this->get_params($params, $fields_map);
    }

}
