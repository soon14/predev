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


class desktop_ctl_finder extends desktop_controller
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
        // 2017-08-10 by shaohj 修改获取app方法 原方法 代码错误
        $get = $_GET;
        !$get && $get = array();
        // 2017-08-24 by shaohj 移除app等
        unset($get['app'], $get['ctl'], $get['act']);
        $post = $_POST;
        !$post && $post = array();
        $params = array_merge($get, $post);
        
        $mdl_obj = app::get($params['app'] ? $params['app'] : 'b2c')->model($params['model']);
        $res = $mdl_obj->getList($params['cols'], $params['filter']);
        if($params['json']) exit(json_encode($res));
        
        // -- start 原方法 有错误 post和get没有合并 而且 $prams['app'] $prams 不是 $params
        //$params = $this->_request->get_params(1);
        //$mdl_obj = app::get($prams['app'] ? $prams['app'] : 'b2c')->model($params['model']);
        //$res = $mdl_obj->getList($params['cols'], $params['filter']);
        // -- end 
        
        
        
        // $schema = $mdl_obj->get_schema();
        // $columns = $schema['columns'];
        // foreach ($res as $key => &$row) {
        //     foreach ($row as $key => $value) {
        //         switch ($columns[$key]) {
        //
        //         }
        //     }
        // }
        $this->pagedata['name'] = $params['name'];
        $this->pagedata['rows'] = $res;
        $this->pagedata['pkey'] = $params['pkey'];
        $this->display('finder/object_row.html');
    }
}
