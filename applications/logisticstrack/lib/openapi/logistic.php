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
class logisticstrack_openapi_logistic extends base_openapi{
    public function __construct()
    {
        $this->req_params = vmc::singleton('base_component_request')->get_params(true);
        $this->token = $this->req_params['token'];
        if(!$this->token){
            $this->failure('无token');
        }
        $this->member = vmc::singleton('docker_member_token')->get_member($this->token);
        if(!$this->member){
            $this->failure('身份不合法');
        }
        $this->push = vmc::singleton('logisticstrack_push');

    }

    //同步物流单号
    public function set_logistic(){
        $logistic_no = $this->req_params['logistic_no'];
        if(!$logistic_no){
            $this->failure('缺少物流单号');
        }
        $corp_code = $this->req_params['corp_code'];
        if(!$corp_code){
            $this->failure('物流公司代码');
        }
        $mdl_log = app::get('logisticstrack') ->model('customer_logistic');
        if($mdl_log ->count(array('logistic_no' =>$logistic_no))){
            $this->failure('该物流单号已存在');
        }
        $data = array(
            'member_id' =>$this->member['member_id'],
            'logistic_no' =>$logistic_no,
            'corp_code' =>strtolower($corp_code),
            'dly_corp' =>$this->req_params['dly_corp'],
            'createtime' =>time(),
        );
        if(!$this->push ->push_logistic($data ,$msg)){
            $this->failure($msg);
        }

        if(!$mdl_log ->save($data)){
            $this->failure('接口错误');
        }
        $this ->success('操作成功');
    }

    //获取物流状态
    public function get_logistic(){
        $logistic_no = $this->req_params['logistic_no'];
        if(!$logistic_no){
            $this->failure('缺少物流单号');
        }
        $key = $this->member['member_id'].'-'.$logistic_no;
        base_kvstore::instance('cache/logistic')->fetch($key, $value);
        if($value){
            $this ->success($value);
        }
        $mdl_log = app::get('logisticstrack') ->model('customer_logistic');
        if(!$data = $mdl_log ->getRow('*',array('logistic_no' =>$logistic_no ,'member_id' =>$this ->member['member_id']))){
            $this->failure('该物流单号不存在');
        }
        $data['pulltimes'] +=1;//累加查询次数
        $mdl_log ->save($data);
        $data['logistic_log'] = unserialize($data['logistic_log']);
        $cache_time = app::get('logisticstrack')->getConf('cache_time');
        if($cache_time>0){
            base_kvstore::instance('cache/logistic')->store($key, $data ,$cache_time);
        }
        $this ->success($data);
    }

    //查询物流 ,实时信息
    public function query(){
        $logistic_no = $this->req_params['logistic_no'];
        if(!$logistic_no){
            $this->failure('缺少物流单号');
        }
        $corp_code = $this->req_params['corp_code'];
        if(!$corp_code){
            $this->failure('物流公司代码');
        }
        if(!$data = $this->push ->query($this->req_params ,$msg)){
            $this->failure($msg);
        }
        $this ->success($data);
    }
}