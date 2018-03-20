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
class marketing_tasks_batchsend extends base_task_abstract implements base_interface_task
{
    public function exec($params=null){
        $message = array(
            'title' =>$params['task']['title'],
            'content' =>$params['task']['content']
        );
        $sender = $params['task']['message_type'] =='sms' ?'b2c_messenger_sms' :'b2c_messenger_email';
        $mdl_members = app::get('b2c')->model('members');
        $member_list = $mdl_members ->getList('member_id ,email ,mobile' ,array('member_id' =>$params['member_id']));
        $message_mdl =app::get('marketing') ->model('message');

        foreach($member_list as $v){
            if($params['task']['message_type'] =='sms' && !$v['mobile']){
                continue;
            }
            if($params['task']['message_type'] =='email' && !$v['email']){
                continue;
            }
            $target = array(
                'member_id' =>$v['member_id'],
                'email' => $v['email'],
                'mobile' => $v['mobile'],
            );
            if(vmc::singleton('b2c_messenger_stage')->send_msg($sender ,$target,$message)){
                $data = array(
                    'task_id' =>$params['task']['task_id'],
                    'member_id' =>$v['member_id'],
                    'group_id' => $params['group_id'],
                    'title' =>$params['task']['title'],
                    'content' =>$params['task']['content'],
                    'message_type' =>$params['task']['message_type'],
                    'create_time' =>time()
                );
                if(!$message_mdl ->save($data)){
                    logger::error('营销信息发送后保存失败'.var_export($data ,1));
                }
            }else{
                logger::error('营销信息发送失败,target:'.var_export($target ,1));
            }
        }
        if($params['all']){
            $task = $params['task'];
            $task_mdl =app::get('marketing') ->model('message_tasks');
            $task['send_status'] = '2';
            if(!$task_mdl ->save($task)){
                logger::error('营销任务状态更新失败2'.var_export($task ,1));
            }
        }
        return true;
    }
}