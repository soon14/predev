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


class materiallib_desktop extends desktop_controller {

    protected $request, $params, $_appid, $_ctrl, $_act;

    function __construct(&$app) {
        parent::__construct($app);
        $this->request = vmc::singleton('base_component_request');
        $this->params = $this->request->get_params(true);

        $this->_appid = $this->app->app_id;
        $this->_ctrl = ($ctrl = $this->request->get_act_name() ? $ctrl : $this->request->get_get('ctl'));
        $this->_act = ($act = $this->request->get_act_name() ? $act : $this->request->get_get('act'));

        $this->pagedata['appid'] = $this->_appid;
        $this->pagedata['ctrl'] = $this->_ctrl;
        $this->pagedata['action'] = $this->_act;
    }

    protected function success($data, $url = false) {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode(array(
            'result' => 'success',
            'url' => $url,
            'data' => $data,
        ));
        exit;
    }

    protected function failure($msg, $url = false) {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode(array(
            'result' => 'failure',
            'data' => array(),
            'url' => $url,
            'msg' => $msg,
        ));
        exit;
    }

}
