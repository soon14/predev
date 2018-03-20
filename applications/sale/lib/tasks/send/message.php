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


class sale_tasks_send_message extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        $mdl_sales = app::get('sale')->model('sales');
        $now = time();
        $end = $now + 3600;
        $sql = "select b.name,b.start,b.end,a.tel,a.member_id from vmc_sale_reserve as a join vmc_sale_sales as b on a.sale_id = b.id where b.status = '0' and b.alert <".$end." and b.alert>=".$now;
        $db = vmc::database();
        $sales = $db->exec($sql);
        if(!empty($sales['rs'])){
            $user_object = vmc::singleton('b2c_user_object');
            foreach($sales['rs'] as $key=>$item){
                $pam_data = $user_object->get_pam_data('*',$item['member_id']);
                $env_list = array(
                    'name'=>$item['name'],
                    'start'=>date('Y-m-d H:i:s',$item['start']),
                    'end'=>date('Y-m-d H:i:s',$item['end']),
                );
                vmc::singleton('b2c_messenger_stage')->trigger('alert_sale',$env_list,array(
                    'email'=>$pam_data['email'],
                    'mobile'=>$item['tel'],
                    'member_id'=>$item['member_id']
                ));
            }
        }
        return true;
    }
}
