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
class  experiencestore_activity_order{

    public function create_tickets($order_id , $nums){
        $mdl_items = app::get('experiencestore') ->model('order_items');
        $mdl_items ->delete(array('order_id' =>$order_id));
        $s = date('YmdHis');
        for($i = 0;$i<$nums ;$i++){
            $data = array(
                'order_id' =>$order_id,
                'sn' =>$s.rand(0000,9999).$i
            );
            if(!$mdl_items ->save($data)){
                return false;
            }
        }
        return true;
    }

    public function schedule_has_buy($member_id ,$schedule_id){
        $db = vmc::database();
        $has_buy = $db ->selectrow("select sum(ticket_nums) as has_buy from vmc_experiencestore_activity_order where
member_id = {$member_id} and schedule_id={$schedule_id}  and enable='true'");
        return $has_buy['has_buy'];
    }

    public function ticket_has_buy($member_id ,$ticket_id){
        $db = vmc::database();
        $has_buy = $db ->selectrow("select sum(ticket_nums) as has_buy from vmc_experiencestore_activity_order where
member_id = {$member_id} and ticket_id={$ticket_id}  and enable='true'");
        return $has_buy['has_buy'];
    }

}