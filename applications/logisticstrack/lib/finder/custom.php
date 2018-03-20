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


class logisticstrack_finder_custom {
    var $column_control = "操作";
    function __construct($app) {
        $this->app = $app;
    }
    function detail_log($id) {
        $data = array();
        $log = $this->app->model('logistic_log')->dump($id);
        $data['logi_log'] = unserialize($log['logistic_log']);
        $render = $this->app->render();
        $render->pagedata['data'] = $data;
        return $render->fetch('admin/logistic_detail.html');
    }

    public function row_style($row) {
    }
}
