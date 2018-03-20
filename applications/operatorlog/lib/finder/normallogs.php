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



class operatorlog_finder_normallogs{

    var $detail_look = '日志详细内容';
    function __construct($app){
        $this->app = $app;
        $this->delimiter = vmc::singleton('operatorlog_service_desktop_controller')->get_delimiter();
    }


    function detail_look($id){
        $obj_logs = $this->app->model('normallogs');
        $render = $this->app->render();
        $memo_basic = $obj_logs->getList('memo', array('id'=>$id));

        if(substr($memo_basic[0]['memo'],0,9) == 'serialize'){
            $memo_arr = explode($this->delimiter, $memo_basic[0]['memo']);
            $memo = unserialize(trim($memo_arr[2]));
            echo $memo_arr[1]."<table class='table'><tr><td><b>键值</b></td><td><b>新值</b></td><td><b>原值</b></td></tr>";
            foreach($memo['new'] as $k => $v){
                echo "<tr>";
                if(is_array($v)){
                    echo "<td>".$k."</td>";
                    echo "<td><pre>";print_r($v);echo "</pre></td>";
                    echo "<td><pre>";print_r($memo['old'][$k]);echo "</pre></td>";
                }else{
                    echo "<td>".$k."</td>";
                    echo "<td>".$v."</td>";
                    echo "<td>".$memo['old'][$k]."</td>";
                }
                echo "</tr>";
            }
            echo "</table>";


        }else{
            echo $memo_basic[0]['memo'];
        }

    }
}
