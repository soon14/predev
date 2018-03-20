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

class vmcservice_ctl_market extends desktop_controller{
    private $vmcservice_base_url = 'http://www.vmcshop.com/';

    public function __construct($app){
        parent::__construct($app);
        if(defined('VMCSERVICE_URL')) {
            $this ->vmcservice_base_url = VMCSERVICE_URL;
        }
    }

    public function app(){
        $access_token = $this->get_access_token();
        $this->pagedata['url'] =$this->vmcservice_base_url.'vmcmarket-app.html?access_token='.$access_token;
        $this->page('admin/market/index.html');
    }

    public function service(){
        $access_token = $this->get_access_token();
        $this->pagedata['url'] =$this->vmcservice_base_url.'vmcmarket-service.html?access_token='.$access_token;
        $this->page('admin/market/index.html');
    }

    public function concat(){
        $this->page('admin/concat.html');
    }

    private function get_access_token(){
        base_kvstore::instance('cache/vmcservice/auth')->fetch(VMCSHOP_TOKEN , $access_token);

        if($access_token){
            return $access_token;
        }
        $url = $this->vmcservice_base_url.'openapi/vmcservice/access_token?token='.VMCSHOP_TOKEN;
        $res = vmc::singleton('base_httpclient')->get($url);
        $res = json_decode($res ,1);
        if($res['result']=='success'){
            $data= $res['data'];
            base_kvstore::instance('cache/vmcservice/auth')->store(VMCSHOP_TOKEN , $data['access_token'] ,12*3600);
            return $data['access_token'];
        }else{
            $this->concat();
            exit;
        }
    }
}
