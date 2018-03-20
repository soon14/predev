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

class universalform_ctl_admin_data extends desktop_controller
{

    public function index()
    {
        $this->finder('universalform_mdl_form_data', array(
            'title' => '表单数据列表',
            'use_buildin_filter' => true,
        ));
    }

    //数据导出页面
    public function export_page() {

        $mdl_form = $this->app->model('form');
        $form_list = $mdl_form->getList('name,form_id');

        $this->pagedata['form_list'] = $form_list;
        $this->page('admin/export.html');

    }

    public function export() {
        $filter = $_POST['filter'];
        foreach ($filter as $key => $value) {
            if (!$value || $value == '') {
                unset($filter[$key]);
            }
            switch ($key) {
                case 'createtime|lthan':
                case 'createtime|bthan':
                    if (!is_numeric($value)) {
                        $filter[$key] = strtotime($value);
                    }
                    break;
            }
        }
        if (empty($filter['createtime|lthan']) || empty($filter['createtime|bthan'])) {
            die('无效的时间段1[必须限定时间段,并且时间跨度不能大于3个月]');
        }
        if ($filter['createtime|bthan'] > $filter['createtime|lthan']) {
            die('无效的时间段2[必须限定时间段,并且时间跨度不能大于3个月]');
        }
        if (($filter['createtime|lthan'] - $filter['createtime|bthan']) > (3600 * 24 * 100)) {
            die('无效的时间段3[必须限定时间段,并且时间跨度不能大于3个月]');
        }

        vmc::singleton('universalform_export_data')->doexport($filter);
    }

}