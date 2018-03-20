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
        return '<a href="index.php?app=system&ctl=admin_crontab&act=edit&_finder[finder_id]=' . $_GET['_finder']['finder_id'] . '&p[0]=' . $row['id'] . '" target="dialog::{title:\'' . '编辑计划任务' . '\', width:680, height:250}">' . '编辑' . '</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="index.php?app=system&ctl=admin_crontab&act=exec&_finder[finder_id]=' . $_GET['_finder']['finder_id'] . '&p[0]=' . $row['id'] . '" >' . '执行' . '</a>';
    }

    function column_rule($row){
        var_dump($row);
        
    }
}
