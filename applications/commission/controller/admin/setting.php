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
class commission_ctl_admin_setting extends desktop_controller
{

    public function index()
    {
        if ($_POST) {
            $this->begin();
            foreach ($_POST as $key => $value) {
                $this->app->setConf($key, $value);
            }
            $this->end(true, '保存成功');
        }

        $member_lv = app::get('b2c')->model('member_lv')->getList("*", array('disabled' => 'false'), 0, -1,
            'experience ASC');
        $member_lv_settings = array();
        foreach($member_lv as $k =>$v){
            $member_lv_settings['lv'.$k] = array(
                'type' => 'text',
                'default' => '0',
                'desc' => $v['name'].'基础分佣比例',
                'class' =>'mode mode-2'
            );
        }
        include($this->app->app_dir . '/setting.php');
        $i = 0;
        foreach ($setting as $kk =>$vv) {
            if($kk == 'mode'){
                $setting = array_merge(array_splice($setting ,0,$i+1) ,$member_lv_settings ,$setting);
            }
            $i++;
        }
        foreach ($setting as $key => $value) {
            if ($value['desc']) {
                $this->pagedata['setting'][$key] = $value;
                $this->pagedata['setting'][$key]['value'] = $this->app->getConf($key);
            }
        }

        $this->page('admin/setting.html');
    }


}