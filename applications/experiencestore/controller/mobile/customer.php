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


class experiencestore_ctl_mobile_customer extends mobile_controller
{
    public $title = '顾客';
    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
        $member_id = vmc::singleton('b2c_user_object')->get_member_id();
        if (!$member_id) {
            $this->splash('error', $this->gen_url(array(
                'app' => 'b2c',
                'ctl' => 'mobile_passport',
                'act' => 'login',
            )), '未登录');
        }
        $this->member_id = $member_id;
        $this->set_tmpl('experiencestore_customer');
    }
    public function index(){
        $this->page('mobile/customer/index.html');
    }
    public function schedule_order()
    {
        $mdl_order = $this->app->model('activity_order');
        $mdl_store = $this->app->model('store');
        $mdl_subject = $this->app->model('activity_subject');
        $mdl_schedule = $this->app->model('activity_schedule');
        $order_list = $mdl_order->getList('*',array('member_id'=>$this->member_id));
        foreach ($order_list as $key => $value) {
            $store_id_arr[] = $value['store_id'];
            $subject_id_arr[] = $value['subject_id'];
            $schedule_id_arr[] = $value['schedule_id'];
        }
        $store_list = $mdl_store->getList('*',array('store_id'=>$store_id_arr));
        $store_list = utils::array_change_key($store_list,'id');
        $subject_list = $mdl_subject->getList('*',array('subject_id'=>$subject_id_arr));
        $subject_list = utils::array_change_key($subject_list,'id');
        $schedule_list = $mdl_schedule->getList('*',array('schedule_id'=>$schedule_id_arr));
        $schedule_list = utils::array_change_key($schedule_list,'id');
        foreach ($schedule_list as $key => &$value) {
            $value['from_time_fmt'] = date('Y-m-d H:i',$value['from_time']);
            $value['to_time_fmt'] = date('Y-m-d H:i',$value['to_time']);
        }
        $this->pagedata['schedule_order_list'] = $order_list;
        $this->pagedata['store_list'] = $store_list;
        $this->pagedata['subject_list'] = $subject_list;
        $this->pagedata['schedule_list'] = $schedule_list;
        $this->page('mobile/customer/schedule_order.html');
    }

    public function cancel_order($order_id){
        $mdl_order = $this->app->model('activity_order');
        $order =  $mdl_order->dump($order_id);
        $redirect_url = $this->gen_url(array(
            'app'=>'experiencestore',
            'ctl'=>'mobile_customer',
            'act'=>'schedule_order'
        ));
        if($order['payed']>0){
            $this->splash('error',$redirect_url,'无法取消预约');
        }
        if($mdl_order->delete(array('id'=>$order_id))){
            $this->splash('success',$redirect_url,'取消预约成功!');
        }
    }

    public function edit_schedule($order_id){
        $mdl_order = $this->app->model('activity_order');
        $order =  $mdl_order->dump($order_id);
        $this->pagedata['schedule_edit_url'] = $this->gen_url(array(
            'app'=>'experiencestore',
            'ctl'=>'mobile_activity',
            'act'=>'schedule',
            'args'=>array($order['store_id'],$order['subject_id'])
        ));
        $this->pagedata['order_id'] = $order_id;
        $this->page('mobile/customer/schedule_edit.html');
    }

    public function add_calendar($schedule_id){
        echo 'TODO';
    }

}
