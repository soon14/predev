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


class vmcconnect_finder_hooktask_items {

    var $column_operation = '操作';
    public $detail_look = '发送内容';

    public function __construct($app) {
        $this->app = $app;
        $this->detail_look = ('操作');
    }

    function column_operation($row){

        return '<a target="_command" class="btn btn-default btn-xs" href="index.php?app=system&ctl=admin_queue&act=send_again&p[0]='.$row['item_id'].'" > '.('再次执行').'</a>';

    }

    function detail_look($id) {
        $_obj = $this->app->model('hooktask_items');
        $render = $this->app->render();
        $_row = $_obj->getRow('send_params', array('item_id' => $id));
        if ($_row) {
            $send_params = unserialize($_row['send_params']);

            echo "<table class='table'><tr><td><b>键名</b></td><td><b>键值</b></td></tr>";

            foreach ($send_params as $_k => $_v) {
                $var = $_v;
                $_tmp_v = is_string($_v) ?json_decode($_v, true) : $_v;
                is_array($_tmp_v) && $var = $_tmp_v;
                echo "<tr>";
                echo "<td>$_k</td>";
                echo "<td><pre>";
                print_r($var);
                echo "</pre></td>";
                echo "</tr>";
            }

            echo "</table>";
        }
    }

}
