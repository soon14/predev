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



class system_finder_crontab {

    var $column_control = '操作';
    var $column_control_order = 'HEAD';

    function __construct($app) {
        $this->app = $app;
    }

    function column_control($row) {
        return '<a class="btn btn-xs btn-default" href="index.php?app=system&ctl=admin_crontab&act=edit&p[0]=' . $row['id'] . '" target="">' . '编辑' . '</a><a target="_command" class="btn btn-xs btn-default" href="index.php?app=system&ctl=admin_crontab&act=exec&p[0]=' . $row['id'] . '" >' . '执行' . '</a>';
    }

}
