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


class ectools_ctl_payment_apps extends desktop_controller {

    public function __construct($app) {
        parent::__construct($app);
        //$this->app = app::get('ectools');
    }

    function index() {
        $mdl_pconf = app::get('ectools')->model('payment_applications');
        $payapp_list = $mdl_pconf->getList('*');
        $this->pagedata['list'] = $payapp_list;
        $this->page('payments/applications/index.html');
    }


    function setting($pay_app_class) {
        if (!$pay_app_class) {
            return false;
        }else{
            $pay_app_instance = new $pay_app_class();
            $setting = $pay_app_instance->setting();
        }
        if ($_POST['setting']) {
            $this->begin('index.php?app=ectools&ctl=payment_apps&act=index');
            foreach ($setting as $key => $value) {
                if($value['file_url'] && $_POST['setting'][$key]) { //有文件地址，需要写入文件
                    if($file_url = $this->file_dir($pay_app_class,$value['file_url'])) {
                        file_put_contents($file_url,trim($_POST['setting'][$key]));
                    };
                }
                $conf[$key] = $_POST['setting'][$key];
            }
            $this->app->setConf($pay_app_class, serialize($conf));
            $this->end(true, '配置成功!');
        } else {
            if ($setting) {
                $render = $this->app->render();
                $render->pagedata['admin_info'] = $pay_app_instance->intro;
                $render->pagedata['app_name'] = $pay_app_instance->name;
                $render->pagedata['app_ver'] = $pay_app_instance->version;
                $render->pagedata['platform_allow'] = $pay_app_instance->platform_allow;
                $render->pagedata['settings'] = $setting;
                $render->pagedata['conf'] = unserialize($this->app->getConf($pay_app_class));
                $render->pagedata['classname'] = $pay_app_class;
                $render->display('payments/applications/cfgs.html');
            }
        }
    }

    private function file_dir($pay_app_class,$extends_url) {
        //按照支付类名 组合路径
        $file_array = explode('_',$pay_app_class);
        array_splice($file_array,1,0,array('lib'));
        $file_array = array_slice($file_array,0,-1);
        $file_url = implode('/',$file_array);
        //开启了扩展目录，写入扩展目录下
        if(defined('EXTENDS_DIR')) {
            $asb_url = EXTENDS_DIR.'/'.$file_url.'/../cert/';
        }else{
            $asb_url = ROOT_DIR.'/applications/'.$file_url.'/../cert/';
        }
        if($extends_url) {
            $asb_url = $asb_url . $extends_url;
        }else{
            return false;
        }
        $mkdir = array_slice(explode('/',$asb_url),0,-1);//去除文件，获取目录
        $mkdir = implode('/',$mkdir);

        if(utils::mkdir_p($mkdir,0777)) {
            return $asb_url;
        };
        return false;
    }
}
