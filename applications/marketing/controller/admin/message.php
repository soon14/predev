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
class marketing_ctl_admin_message extends desktop_controller
{


    public function sms_tasks ()
    {
        $base_filter =array('message_type'=>'sms');
        $this->finder ('marketing_mdl_message_tasks', array(
            'title' => '短信营销',
            'base_filter' =>$base_filter,
            'actions' => array(
                array(
                    'label' => '新建营销',
                    'href' => 'index.php?app=marketing&ctl=admin_message&act=edit_task&p[0]=sms'
                )
            )
        ));
    }

    public function email_tasks ()
    {
        $base_filter =array('message_type'=>'email');
        $this->finder ('marketing_mdl_message_tasks', array(
            'title' => '邮件营销',
            'base_filter' =>$base_filter,
            'actions' => array(
                array(
                    'label' => '新建营销',
                    'href' => 'index.php?app=marketing&ctl=admin_message&act=edit_task&p[0]=email'
                )
            )
        ));
    }


    public function sms_tmpl ()
    {
        $base_filter =array('message_type'=>'sms');
        $this->finder ('marketing_mdl_message_tmpl', array(
            'title' => '短信营销模板',
            'use_buildin_recycle' => true,
            'base_filter' =>$base_filter,
            'actions' => array(
                array(
                    'label' => '新建模板',
                    'href' => 'index.php?app=marketing&ctl=admin_message&act=edit_tmpl&p[0]=sms'
                )
            )
        ));
    }

    public function email_tmpl ()
    {
        $base_filter = array('message_type' => 'email');
        $this->finder ('marketing_mdl_message_tmpl', array(
            'title' => '邮件营销模板',
            'use_buildin_recycle' => true,
            'base_filter' => $base_filter,
            'actions' => array(
                array(
                    'label' => '新建模板',
                    'href' => 'index.php?app=marketing&ctl=admin_message&act=edit_tmpl&p[0]=email'
                )
            )
        ));
    }

    public function edit_tmpl($type,$tmpl_id){
        if($tmpl_id){
            $this ->pagedata['tmpl'] = $this ->app ->model('message_tmpl')->dump($tmpl_id);
        }
        $this ->pagedata['type'] = $type;
        $this ->page('admin/message/'.$type.'_tmpl.html');
    }

    public function save_tmpl(){
        $data = $_POST;
        if(!$data['tmpl_id']){
            $data['create_time'] =time();
        }
        $message_mdl = $this ->app ->model('message_tmpl');
        if(!$message_mdl->save($data)){
            $this ->splash('error','','保存失败');
        }

        $this ->splash('success','','保存成功');
    }



    public function edit_task($type,$task_id){
        if($task_id){
            $task = $this ->app ->model('message_tasks')->dump($task_id);
            $this ->pagedata['tmpl'] = $this ->app ->model('message_tmpl')->dump($task['tmpl_id']);
            $this ->pagedata['extend_group'] = $this ->app ->model('group')->getList('*',array('group_id'=>$task['group_id']));
            $this ->pagedata['task'] =$task;
        }
        $this ->pagedata['type'] = $type;
        $this ->pagedata['base_filter'] =array('status'=>'1');
        $this ->pagedata['base_tmpl_filter'] =array('message_type'=>$type);
        $this ->page('admin/message/task.html');
    }

    public function save_task(){
        $data = $_POST;
        $send_now = false;
        $this ->begin();
        $task_mdl=$this ->app ->model('message_tasks');
        if(!$data['task_id']){
            $data['create_time'] =time();
            if(!$data['send_time']){
                $send_now = true;//立即执行发送
            }
        }else{
            $task = $task_mdl->dump($data['task_id']);
            if($task['send_status']!='0'){
                $this ->end(false ,'该任务已经开始执行，不能修改');
            }
        }
        $data['send_time'] = $data['send_time'] ? strtotime($data['send_time']) :time();
        $data['group_id'] = $_POST['conditions']['group_id'];
        unset($data['conditions']);
        $tmpl = $this ->app ->model('message_tmpl')->dump($data['tmpl_id']);
        $data['title'] = $tmpl['title'];
        $data['content'] = $tmpl['content'];

        if(! $task_mdl->save($data)){
            $this ->end(false,'保存失败');
        }
        $data = $this ->convert_content($data);
        if(!$task_mdl->save($data)){
            $this ->end(false,'保存失败');
        }
        $report =array(
            'task_id'=> $data['task_id'],
            'name'=>$data['name'],
        );

        if(!$this ->app->model('report')->save($report)){
            logger::error('营销效果报告记录失败');
            return false;
        }
        if($send_now){
            system_queue::instance()->publish('marketing_tasks_send', 'marketing_tasks_send', $data);
        }
        $data['send_time'] = date('Y-m-d H:i' ,$data['send_time']);
        $this ->end(true,'保存成功','',$data);
    }

    public function ajax_tmpl(){
        $this ->pagedata['tmpl'] = $this ->app ->model('message_tmpl') ->getRow('*' ,$_POST);
        $this ->display('admin/message/ajax_tmpl.html');
    }

    public function send_test($type){
        $account = $_POST['account'];
        $task_id = $_POST['task_id'];
        $task = $this ->app ->model('message_tasks')->dump($task_id);
        if(!$task){
            $this ->splash('error' ,'' ,'请先完善营销信息');
        }
        $params =array(
            'title' =>$task['title'],
            'content' =>$task['content']
        );
        if($type=='sms'){
            $res = vmc::singleton('b2c_messenger_stage')->send_msg('b2c_messenger_sms' ,array('mobile'=>$account),$params);
        }elseif($type=='email'){
            $res = vmc::singleton('b2c_messenger_stage')->send_msg('b2c_messenger_email' ,array('email'=>$account),$params);
        }else{
            $this ->splash('error' ,'' ,'未知帐号类型');
        }
        if($res){
            $this ->splash('success' ,'' ,'发送成功');
        }else{
            $this ->splash('error' ,'' ,'发送失败');
        }
    }

    private function convert_content( $data){
        if($data['message_type']=='sms'){
            preg_match_all('/\[short\](\S+?)\[\/short\]/',$data['content'],$match);
            if(!empty($match)){
                foreach($match[1] as $v){
                    $replace[]=$this ->convert_url($v ,$data['task_id'],1);
                }
                $data['content']=str_ireplace($match[0] ,$replace,$data['content'] );
            }

            preg_match_all('/\[url\](\S+?)\[\/url\]/',$data['content'],$match_url);
            if(!empty($match_url)){
                foreach($match_url[1] as $v){
                    $replace_url[]=$this ->convert_url($v ,$data['task_id']);
                }
                $data['content']=str_ireplace($match_url[0] ,$replace_url,$data['content'] );
            }
        }else{
            preg_match_all('/<\s*a\s.*?href\s*=\s*([\"\'])?(?(1) (.*?)\\1 | ([^\s\>]+))/isx',$data['content'],$match);
            if(!empty($match)){
                foreach($match[2] as $v){
                    $replace_url[]=$this ->convert_url($v ,$data['task_id']);
                }
                $data['content']=str_ireplace($match[2] ,$replace_url,$data['content'] );

            }
        }
        return $data;
    }

    public function convert_url($url,$task_id ,$is_short=false){
        if(stripos($url , 'javascript') !==false){
            return $url;
        }
        $url_arr = parse_url($url);
        $url.=($url_arr['query']? '&':'?').'taskno='.$task_id;

        if($is_short){
            $new_url = vmc::singleton("marketing_shorturl")->get_short($url);
        }
        return $new_url ?$new_url :$url;

    }

}