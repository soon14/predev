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


class system_ctl_admin_crontab extends desktop_controller
{
    public function index()
    {
        $params = array(
            'title' => '计划任务管理',
            'use_buildin_recycle' => false,
            'use_buildin_refresh' => true,
            
        );
        $this->finder('base_mdl_crontab', $params);
    }

    public function edit($cron_id)
    {
        $model = app::get('base')->model('crontab');
        $cron = $model->dump($cron_id);
        $this->pagedata['cron'] = $cron;
        $this->display('admin/crontab/detail.html');
    }

    public function save()
    {
        $this->begin('index.php?app=system&ctl=admin_crontab&act=index');
        $model = app::get('base')->model('crontab');
        if ($model->update(array('schedule' => $_POST['schedule'],'enabled'=>$_POST['enabled'],'is_unique'=>$_POST['is_unique']),
                           array('id' => $_POST['id']))) {
            $this->end(true, '保存成功');
        } else {
            $this->end(false, '保存失败');
        }
    }

    public function exec($cron_id)
    {
        $this->begin('index.php?app=system&ctl=admin_crontab&act=index');
        $model = app::get('base')->model('crontab');
        $cron = $model->getRow('id', array('id' => $cron_id));
        if (!$cron || (base_crontab_schedule::trigger_one($cron['id']) === false)) {
            $this->end(false, '执行失败');
        }
        $this->end(true, '执行成功');
    }
}
