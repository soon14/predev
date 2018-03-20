<?php

/**
 * Created by PhpStorm.
 * User: cp
 * Date: 2017/5/24
 * Time: 16:25
 */

class preselling_finder_activity
{
    var $column_control = '操作';

    var $column_activity_status = '状态';

    function column_control($row){
        $btn  = '<a   class="btn btn-xs btn-default"   href="index.php?app=preselling&ctl=admin_index&act=edit&p[0]='.$row['activity_id'].'">'.('编辑').'</a>';
        return $btn;
    }


    public function column_activity_status($row) {
        $time = time();
        if($time < $row['deposit_starttime']) {
            return '未开始';
        }elseif($time>$row['deposit_starttime'] && $time<$row['deposit_endtime']){
            return '订金支付阶段';
        }elseif($time>$row['balance_starttime'] && $time<$row['balance_endtime']){
            return '尾款支付阶段';
        }else{
            return '已结束';
        }
    }
    


}
