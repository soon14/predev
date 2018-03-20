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



class system_finder_queue {

    var $column_control = '操作';
    var $detail_params = '任务参数详情';
    var $column_control_order = 'HEAD';

    function __construct($app) {
        $this->app = $app;
    }

    function detail_params($id, $params) {
        if (!$params && $id) {
            $task = app::get('system')->model('queue_mysql')->dump($id);
            $params = unserialize($task['params']);
            echo "<h4>任务参数：</h4>";
        }
        
        echo "<table class='table'>";
        foreach ($params as $key => $value) {
            echo "<tr><td>$key : </td>";
            if (is_array($value)) {
                echo "<td>";
                self::detail_params(false, $value);
                echo "</td>";
            } elseif (!empty($value)) {
                echo "<td>$value</td>";
            } else {
                echo "<td>空</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }



    function column_control($row) {

        $queue_controller_name = system_queue::get_controller_name();
        $support_queue_controller_name = 'system_queue_adapter_mysql';

        if ($queue_controller_name == $support_queue_controller_name) {
            return "<a target='_command' class='btn btn-xs btn-default' href='index.php?app=system&ctl=admin_queue&act=retry&p[0]=".$row['id']."'>手动执行</a>";
        }else{
            return "";
        }

    }


}
