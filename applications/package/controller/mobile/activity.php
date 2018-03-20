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


class codebuy_ctl_mobile_activity extends b2c_mfrontpage
{
    public function __construct($app)
    {
        parent::__construct($app);
        $this->params = utils::_filter_input($_POST);
        vmc::singleton('base_session')->start();
        unset($_POST);
    }
    public function check_code(){
        if(!$this->params['goods_id'] || !$this->params['activity_id'] || !$this->params['product_id']){
            echo '数据不完整';
            exit;
        }
        $member_id = vmc::singleton('b2c_user_object')->get_member_id();
        $mdl_log = $this->app->model('log');
        $log = $mdl_log->getRow('id,order_id',array('member_id'=>$member_id,'activity_id'=>$this->params['activity_id'],'goods_id'=>$this->params['goods_id'],'order_id'=>0));
        $this->pagedata['goods_id'] = $this->params['goods_id'];
        $this->pagedata['product_id'] = $this->params['product_id'];
        $this->pagedata['activity_id'] = $this->params['activity_id'];
        if(empty($log)){
            echo $this->fetch('mobile/product/code_btn.html');
        }else{
            echo $this->fetch('mobile/product/buy_btn.html');
        }
    }
    public function code_form($goods_id,$activity_id,$product_id){
        $this->title = '请输入您的优购码';
        if(!$goods_id || !$activity_id || !$product_id){
            echo '数据不完整';
            exit;
        }
        $this->pagedata['goods_id'] = $goods_id;
        $this->pagedata['product_id'] = $product_id;
        $this->pagedata['activity_id'] = $activity_id;
        $this->page('mobile/product/code_form.html');
    }
    public function use_code(){
        if(!$this->params['activity_id']){
            $this->splash('error',null,'没有活动id');
        }
        if(!$this->params['code']){
            $this->splash('error',null,'请输入优购码');
        }
        if(!$this->params['vcode']){
            $this->splash('error',null,'请输入验证码');
        }
        if (!$this->params['vcode'] || !base_vcode::verify('codebuy', $this->params['vcode'])) {
            $this->splash('error',null,'验证码错误');
        }
        $member_id = vmc::singleton('b2c_user_object')->get_member_id();
        $mdl_code = $this->app->model('code');
        $mdl_log = $this->app->model('log');
        $code = $mdl_code->getRow('id,status',array('code'=>$this->params['code'],'activity_id'=>$this->params['activity_id']));
        if(empty($code)){
            $this->splash('error',null,'您输入的优购码不正确');
        }
        if($code['status'] == '1'){
            $this->splash('error',null,'该优购码已经被使用');
        }
        $code_data = array(
            'status'=>'1',
        );
        $log_data = array(
            'activity_id'=>$this->params['activity_id'],
            'code_id'=>$code['id'],
            'member_id'=>$member_id,
            'usetime'=>time(),

        );
        $flag1 = $mdl_code->update($code_data,array('id'=>$code['id']));
        $flag2 = $mdl_log->save($log_data);
        if($flag1 && $flag2){
            $url = app::get('mobile')->router()->gen_url(array(
                        'app'=>'b2c',
                        'ctl'=>'mobile_product',
                        'args'=>array($this->params['product_id'])
                   ));
            $this->splash('success',$url,'验证成功');
        }else{
            $this->splash('error',null,'保存失败');
        }
    }
}
