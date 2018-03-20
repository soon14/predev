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


class universalform_openapi_forms extends base_openapi {

    private $req_params = array();

    public function __construct() {
        $this->req_params = vmc::singleton('base_component_request')->get_params(true);
    }

    public function get($params = array()) {
        $params = array_merge(($params ? $params : array()), ($this->req_params ? $this->req_params : array()));
        $cache_key = utils::array_md5($params);
        //优先从缓存读取
        if (cachemgr::get($cache_key, $form_data)) {
            //$this->success($form_data);
        }
        cachemgr::co_start();

        $filter = $params['filter'];
        //myfun::vard($filter, $params);

        if (!is_array($filter)) {
            $filter = array();
        }

        if (!$filter) {
            $this->failure('参数错误');
        }

        $mdl_form = app::get('universalform')->model('form');

        $mdl_form_model = app::get('universalform')->model('form_module');
        $form_data = $mdl_form->getList('*', $filter);
        if (!$form_data) $this->success(array());

        $fids = array_keys(utils::array_change_key($form_data, 'form_id'));
        $form_model_data = $mdl_form_model->getList('*', array('form_id' => $fids));

        foreach ($form_data as $k => $v) {
            foreach ($form_model_data as $_k => $_v) {
                if($_v['form_id'] == $v['form_id']){
                    $form_data[$k]['items'][] = $_v;
                }
            }
        }

        cachemgr::set($cache_key, $form_data, cachemgr::co_end());
        $this->success($form_data);
    }

}
