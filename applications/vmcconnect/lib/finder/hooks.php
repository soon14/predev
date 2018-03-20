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


class vmcconnect_finder_hooks {

    public $column_acts = '操作';
    public $column_acts_order = 0;
    
    public $detail_look = '查看';

    public function __construct($app) {
        $this->app = $app;
        $this->column_acts = ('操作');
        $this->detail_look = ('查看');
    }

    public function column_acts($row) {
        $btn = '';
        if (vmc::singleton('desktop_user')->has_permission('vmcconnect_hooks_edit')) {
            $btn = '<a href="index.php?app=vmcconnect&ctl=admin_hooks&act=edit&p[0]=' . $row['app_id'] . '&p[1]=' . $row['hook_id'] . '" class="btn btn-default btn-xs"><i class="fa  fa-edit"></i> 编辑</a>';
        }
        return $btn;
    }
    
    function detail_look($hook_id) {
        $_obj = $this->app->model('hooks');
        $render = $this->app->render();
        $_row = $_obj->getRow('app_id', array('hook_id' => $hook_id));
        $app_id = $_row ? $_row[0]['app_id'] : 0;
        echo "<table class='table'><tr><td><b>设置日志</b></td><td><b>执行日志</b></td></tr>";

        echo "<tr>";
        echo "<td><a href=\"index.php?app=vmcconnect&ctl=admin_hooks&act=logs&p[0]=$app_id&p[1]=$hook_id&p[2]=setting\" class=\"btn btn-default btn-xs\"><i class=\"fa  fa-edit\"></i> 查看设置日志</a></td>";
        echo "<td><a href=\"index.php?app=vmcconnect&ctl=admin_hooks&act=queues&p[0]=$app_id&p[1]=$hook_id\" class=\"btn btn-default btn-xs\"><i class=\"fa  fa-eye\"></i> 查看执行日志</a></td>";
        echo "</tr>";

        echo "</table>";
    }

}
