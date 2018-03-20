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
class universalform_ctl_site_form extends b2c_frontpage
{
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $user_obj = vmc::singleton('b2c_user_object');
        $this->member_id = $user_obj->get_member_id();
    }

    public function index($form_id) {
        $mdl_form = $this->app->model('form');
        $form = $mdl_form->dump($form_id,'*','default');
        if(!$form['form_id']) {
            $this->splash('error','','未知表单');
        }
        $this->pagedata['form'] = $form;
        $this->page('site/index.html');
    }

    public function save() {
        $mdl_form = $this->app->model('form');
        $form_id = $_POST['form_id'];
        if(!$form_id) {
            $this->splash('error','','未知表单');
        }
        $data['data'] = $_POST;
        $data['form_id'] = $form_id;
        $data['createtime'] = time();
        $data['member_id'] = $this->member_id;
        if(!$mdl_form->check($data,$msg)) {
            $this->splash('error','',$msg);
        };

        $mdl_form_data = $this->app->model('form_data');
        if(!$mdl_form_data->save($data)) {
            $this->splash('error','','保存失败');
        };
        foreach(vmc::servicelist('universalform.data_success') as $obj) {
            if(!$obj->exec($data,$msg)) {
                logger::error('表单信息保存成功以后错误：'.$msg);
            };
        }
        $this->splash('success','','保存成功');
    }

    //发送身份识别验证码
    public function vmobile(){
        $mobile = trim($_POST['mobile']);
        if(!$vcode = vmc::singleton('b2c_user_vcode')->set_vcode($mobile,'universalform',$msg)){
            $this->splash('error', null, $msg);
        }
        $data['vcode'] = $vcode;
        $send_flag = vmc::singleton('b2c_user_vcode')->send_sms('activation',(string)$mobile,$data);
        if(!$send_flag){
            $this->splash('error', null, '发送失败');
        }
        $this->splash('success', null, '发送成功');

    }

}