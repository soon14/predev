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


class logisticstrack_ctl_admin_tracker extends desktop_controller {
    public function __construct($app) {
        parent::__construct($app);
    }
    public function index() {
        $this->finder('logisticstrack_mdl_logistic_log', array(
            'title' => '物流状态跟踪列表' ,
            'use_buildin_filter'=>true,
            'actions' => array(
                array(
                    'label' => ('设置物流状态同步API') ,
                    'icon' => 'fa fa-cog',
                    'href' => 'index.php?app=logisticstrack&ctl=admin_tracker&act=apiset',
                ) ,
            )
        ));

    }
    public function apiset() {
        if ($_POST) {
            $this->begin('index.php?app=logisticstrack&ctl=admin_tracker&act=index');
            $system_order_tracking = $_POST['system_order_tracking'];
            $kuaidi100Key = trim($_POST['kuaidi100Key']);
            $kuaidi100delay = trim($_POST['kuaidi100delay']);
            app::get('b2c')->setConf('system.order.tracking', $system_order_tracking);
            app::get('logisticstrack')->setConf('kuaidi100Key', $kuaidi100Key);
            app::get('logisticstrack')->setConf('kuaidi100delay', $kuaidi100delay);
            app::get('logisticstrack')->setConf('key_type', trim($_POST['key_type']));
            app::get('logisticstrack')->setConf('cache_time', trim($_POST['cache_time']));
            app::get('logisticstrack')->setConf('customer' ,trim($_POST['customer']));
            $this->pagedata['system_order_tracking'] = $system_order_tracking;
            $this->pagedata['kuaidi100Key'] = $kuaidi100Key;

            $this->end(true,'保存成功!');
        } else {
            $this->pagedata['system_order_tracking'] = app::get('b2c')->getConf('system.order.tracking');
            $this->pagedata['kuaidi100Key'] = app::get('logisticstrack')->getConf('kuaidi100Key');
            $this->pagedata['kuaidi100delay'] = app::get('logisticstrack')->getConf('kuaidi100delay');
            $this->pagedata['key_type'] = app::get('logisticstrack')->getConf('key_type');
            $this->pagedata['cache_time'] = app::get('logisticstrack')->getConf('cache_time');
            $this->pagedata['customer'] = app::get('logisticstrack') ->getConf('customer');
        }
        $this->page('admin/setting.html');
    }
    public function pull($deliveryid) {

        $data = vmc::singleton('logisticstrack_puller')->pull($deliveryid,$errmsg);
        $this->pagedata['data'] = $data;
        $this->pagedata['errmsg'] = $errmsg;

        $this->display('admin/logistic_detail.html');
    }

    public function pull_atonce($deliveryid){
        $this->begin();
        $res = vmc::singleton('logisticstrack_puller')->pull($deliveryid,$errmsg,true);
        $this->end(!$errmsg,$errmsg);
    }
}
