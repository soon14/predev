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


class o2ocds_ctl_finder extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_request = vmc::singleton('base_component_request');
    }
    public function object_select()
    {
        $finder_mdl = $this->_request->get_get('finder_mdl');
        $filter = $this->_request->get_get('base_filter');
        $multiple = $this->_request->get_get('multiple');

        $this->finder($finder_mdl, array(
            'object_select_model' => true,
            'selectrow_type' => ($multiple == 'true' ? 'checkbox' : 'radio'),
            'base_filter'=>$filter
        ));
    }
    public function object_row()
    {
        $params = $this->_request->get_params(1);
        $mdl_obj = app::get($params['app_name'] ? $params['app_name'] : 'b2c')->model($params['model']);
        $res = $mdl_obj->getList($params['cols'], $params['filter']);

        $this->pagedata['name'] = $params['name'];
        $this->pagedata['rows'] = $res;
        $this->pagedata['pkey'] = $params['pkey'];
        $this->display('finder/object_row.html');
    }
}
