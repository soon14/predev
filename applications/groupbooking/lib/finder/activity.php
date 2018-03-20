<?php

/**
 * Created by PhpStorm.
 * User: cp
 * Date: 2017/5/24
 * Time: 16:25
 */

class groupbooking_finder_activity
{
    var $column_control = '操作';
    var $column_participate_number = '参与人次';
    var $column_stay_order = '待成团订单';
    var $column_order = '成团订单';
    var $column_activity_status = '状态';

    function column_control($row){
        $btn  = '<a   class="btn btn-xs btn-default"   href="index.php?app=groupbooking&ctl=admin_index&act=edit&p[0]='.$row['activity_id'].'">'.('编辑').'</a>';
        return $btn;
    }

    public function column_participate_number($row) {
        return app::get('groupbooking')->model('participate_member')->count(array('activity_id'=>$row['activity_id']));
    }

    public function column_stay_order($row) {
        return app::get('groupbooking')->model('orders')->count(array('activity_id'=>$row['activity_id'],'status'=>'0','main_id'=>'0'));
    }

    public function column_order($row) {
        return app::get('groupbooking')->model('orders')->count(array('activity_id'=>$row['activity_id'],'status'=>'1','main_id'=>'0'));
    }

    public function column_activity_status($row) {
        $time = time();
        if($time < $row['start_time']) {
            return '未开始';
        }elseif($time>$row['start_time'] && $time<$row['end_time']){
            return '进行中';
        }else{
            return '已结束';
        }
    }


}
