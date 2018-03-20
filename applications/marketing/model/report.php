<?php
// +----------------------------------------------------------------------
// | VMCSHOP [V M-Commerce Shop]
// +----------------------------------------------------------------------
// | Copyright (c) vmcshop.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.vmcshop.com/licensed)
// +----------------------------------------------------------------------
// | Author: Shanghai ChenShang Software Technology Co., Ltd.
// +---------------------------------------------------------------------
class marketing_mdl_report extends dbeav_model{

    public function modifier_send_nums($col ,$row){

        if($row['send_time'] && !$col){
            $num = app::get('marketing')->model('message')->count(array('task_id'=>$row['task_id']));
            if($num){
                $this ->update(array('send_nums'=> $num),array('task_id'=>$row['task_id']));
            }
            return $num;
        }else{
            return $col;
        }
    }
}