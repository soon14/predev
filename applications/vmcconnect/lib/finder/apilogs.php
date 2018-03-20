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


class vmcconnect_finder_apilogs {

    public $detail_look = '日志详细内容';

    public function __construct($app) {
        $this->app = $app;
        $this->column_acts = ('操作');
    }

    function detail_look($id) {
        $obj_logs = $this->app->model('apilogs');
        $render = $this->app->render();
        $log_row = $obj_logs->getRow('ori_in_params, code_in_params, ori_out_params, code_out_params', array('log_id' => $id));

        if ($log_row) {
            $ori_in_params = unserialize($log_row['ori_in_params']);
            $code_in_params = unserialize($log_row['code_in_params']);
            $ori_out_params = unserialize($log_row['ori_out_params']);
            $code_out_params = unserialize($log_row['code_out_params']);
            
            echo "<table class='table'><tr><td><b>键名</b></td><td><b>键值</b></td></tr>";
            
            echo "<tr>";
            echo "<td>原始参数</td>";
            echo "<td><pre>";
            print_r($ori_in_params);
            echo "</pre></td>";
            echo "</tr>";
            
            echo "<tr>";
            echo "<td>转换后参数</td>";
            echo "<td><pre>";
            print_r($code_in_params);
            echo "</pre></td>";
            echo "</tr>";
            
            echo "<tr>";
            echo "<td>原始返回数据</td>";
            echo "<td><pre>";
            print_r($ori_out_params);
            echo "</pre></td>";
            echo "</tr>";
            
            echo "<tr>";
            echo "<td>转换后返回数据</td>";
            echo "<td><pre>";
            print_r($code_out_params);
            echo "</pre></td>";
            echo "</tr>";
            
            echo "</table>";
        }
    }

}
