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


class universalform_mdl_form extends dbeav_model {
    var $has_many = array(
        'modules'=>'form_module',
    );
    public $subSdf = array(
        'default' => array(
            'modules' => array(
                '*',
            ),
        ) ,
    );

    public function check($data,&$msg) {
        if(!$form = $this->dump($data['form_id'],'*','default')) {
            $msg = '未知表单';
            return false;
        };
        //判断比填项不能为空
        foreach($form['modules'] as $module) {
            if($module['show'] == 'true') {
                if($module['required'] == 'true') {
                    if(!$data['data'][$module['name']]) {
                        $msg = $module['module_name'].' 不能为空';
                        return false;
                    }
                }
            }
        }
        /*if($form['times_submit'] == 'false') {
            $submit_count = $this->app->model('form_data')->count(array('form_id'=>$data['data']['form_id']));
            if($submit_count >= 1) {
                $msg = '已提交过';
                return false;
            }
        }*/
        if($form['vcode'] == 'true') {
            if(!base_vcode::verify('universalform', $data['data']['vcode'])) {
                $msg = '图形验证码错误！';
                return false;
            }
        }
        if($form['vmobile'] == 'true') {
            if(!vmc::singleton('b2c_user_vcode')->verify($data['data']['vmobile'], $data['data']['mobile'],'universalform')){
                $msg = '手机验证码错误！';
                return false;
            }
        }

        return true;
    }



}
