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


class logisticstrack_finder_log {
    var $column_control = "操作";
    function __construct($app) {
        $this->app = $app;
    }
    function detail_log($id) {
        $data = array();
        $log = $this->app->model('logistic_log')->dump($id);
        vmc::singleton('logisticstrack_puller')->pull($log['delivery_id'], $errmsg); //强制刷新
        $log = $this->app->model('logistic_log')->dump($id);
        $data['logi_log'] = unserialize($log['logistic_log']);
        $render = $this->app->render();
        $render->pagedata['data'] = $data;
        return $render->fetch('admin/logistic_detail.html');
    }
    public function column_control($row) {
        //return $row['delivery_id'];
        return '<a href="index.php?app=logisticstrack&ctl=admin_tracker&act=pull_atonce&p[0]=' . $row['delivery_id'] . '" class="btn btn-xs btn-default" target="_command"><i class="fa fa-refresh"></i> 立即同步</a>';
    }
    public function row_style($row) {
    }
}
