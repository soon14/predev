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


class universalform_finder_form_data
{
    public $detail_data = '表单数据详情';
    public $column_form_name = '表单名称';

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function column_form_name($row) {

        return app::get('universalform')->model('form')->getRow('name',array('form_id'=>$row['@row']['form_id']))['name'];
    }

    public function detail_data($data_id) {
        $render = $this->app->render();
        $mdl_form_data = app::get('universalform')->model('form_data');
        $mdl_form_module = app::get('universalform')->model('form_module');
        $form_data = $mdl_form_data->dump($data_id);
        $form_modules = $mdl_form_module->getList('*',array('form_id'=>$form_data['form_id']));
        $render->pagedata['modules']  = $form_modules;
        $render->pagedata['data']  = $form_data;
        return $render->fetch('admin/data/detail.html');
    }


    public function row_style($row)
    {
        //$row = $row['@row'];
    }
}
