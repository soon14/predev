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


class vmcconnect_finder_apps {

    public $column_acts = '操作';
    public $column_acts_order = 0;
    public $column_api_set = 'API管理';
    public $column_api_set_order = 1;
    public $column_hook_set = 'HOOK管理';
    public $column_hook_set_order = 2;
    public $detail_look = '查看';

    public function __construct($app) {
        $this->app = $app;
        $this->column_acts = ('操作');
        $this->column_api_set = ('API管理');
        $this->column_hook_set = ('HOOK管理');
        $this->detail_look = ('查看');
    }

    function detail_look($app_id) {
        echo "<table class='table'><tr><td><b>设置日志</b></td><td><b>API设置日志</b></td><td><b>HOOK设置日志</b></td><td><b>HOOK执行日志</b></td><td><b>API服务日志</b></td></tr>";

        echo "<tr>";
        echo "<td><a href=\"index.php?app=vmcconnect&ctl=admin_apps&act=logs&p[0]=$app_id&p[1]=setting\" class=\"btn btn-default btn-xs\"><i class=\"fa  fa-edit\"></i> 查看设置日志</a></td>";
        echo "<td><a href=\"index.php?app=vmcconnect&ctl=admin_apps&act=logs&p[0]=$app_id&p[1]=api\" class=\"btn btn-default btn-xs\"><i class=\"fa  fa-edit\"></i> 查看API设置日志</a></td>";
        echo "<td><a href=\"index.php?app=vmcconnect&ctl=admin_apps&act=logs&p[0]=$app_id&p[1]=hook\" class=\"btn btn-default btn-xs\"><i class=\"fa  fa-edit\"></i> 查看HOOK设置日志</a></td>";
        echo "<td><a href=\"index.php?app=vmcconnect&ctl=admin_apps&act=queues&p[0]=$app_id\" class=\"btn btn-default btn-xs\"><i class=\"fa  fa-eye\"></i> 查看HOOK执行日志</a></td>";
        echo "<td><a href=\"index.php?app=vmcconnect&ctl=admin_apps&act=apilog&p[0]=$app_id\" class=\"btn btn-default btn-xs\"><i class=\"fa  fa-eye\"></i> 查看API服务日志</a></td>";
        echo "</tr>";

        echo "</table>";
    }

    public function column_acts($row) {
        $btn = '';
        if (vmc::singleton('desktop_user')->has_permission('vmcconnect_apps_edit')) {
            $btn = '<a href="index.php?app=vmcconnect&ctl=admin_apps&act=edit&p[0]=' . $row['app_id'] . '" class="btn btn-default btn-xs"><i class="fa  fa-edit"></i> 编辑</a>';
        }
        return $btn;
    }

    public function column_api_set($row) {
        $btn = '';
        if (vmc::singleton('desktop_user')->has_permission('vmcconnect_apps_apis')) {
            $btn = '<a href="index.php?app=vmcconnect&ctl=admin_apps&act=apis&p[0]=' . $row['app_id'] . '" class="btn btn-default btn-xs"><i class="fa  fa-edit"></i> 管理</a>';
        }
        return $btn;
    }

    public function column_hook_set($row) {
        $btn = '';
        if (vmc::singleton('desktop_user')->has_permission('vmcconnect_apps_hooks')) {
            $btn = '<a href="index.php?app=vmcconnect&ctl=admin_apps&act=hooks&p[0]=' . $row['app_id'] . '" class="btn btn-default btn-xs"><i class="fa  fa-edit"></i> 管理</a>';
        }

        if (vmc::singleton('desktop_user')->has_permission('vmcconnect_hooks')) {
            $btn .= '<a href="index.php?app=vmcconnect&ctl=admin_hooks&act=index&p[0]=' . $row['app_id'] . '" class="btn btn-default btn-xs"><i class="fa  fa-edit"></i> 服务管理</a>';
        }
        return $btn;
    }

}
