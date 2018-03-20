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


class community_ctl_admin_user_lv extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
        header('cache-control: no-store, no-cache, must-revalidate');
    }

    public function index()
    {
        $actions = array(
            array(
                'icon' => 'fa-plus',
                'label' => ('添加等级'),
                'href' => 'index.php?app=community&ctl=admin_user_lv&act=addnew',
            ),
        );
        $this->finder('community_mdl_user_lv', array(
                'title' => '用户等级',
                'use_buildin_recycle' => true,
                'actions' => $actions,
            ));
    }

    public function addnew($user_lv_id = null)
    {
        if ($user_lv_id != null) {
            $mem_lv = $this->app->model('user_lv');
            $aLv = $mem_lv->dump($user_lv_id);
            $aLv['default_lv_options'] = array('1' => ('是'),'0' => ('否'));
            $this->pagedata['lv'] = $aLv;
        }

        $this->page('admin/user/lv.html');
    }

    public function save()
    {
        $end_go = vmc::router()->gen_url(array('app' => 'community', 'ctl' => 'admin_user_lv', 'act' => 'index'));
        $this->begin($end_go);
        $mdl_user_lv = $this->app->model('user_lv');
        if ($mdl_user_lv->validate($_POST, $msg)) {
            if ($_POST['user_lv_id']) {
                $olddata = app::get('community')->model('user_lv')->dump($_POST['user_lv_id']);
            }
            if ($mdl_user_lv->save($_POST)) {
                $this->end(true, ('保存成功'));
            } else {
                $this->end(false, ('保存失败'));
            }
        } else {
            $this->end(false, $msg);
        }
    }

    public function setdefault($lv_id)
    {
        $end_go = vmc::router()->gen_url(array('app' => 'community', 'ctl' => 'admin_user_lv', 'act' => 'index'));
        $this->begin($end_go);
        $mdl_user_lv = $this->app->model('user_lv');
        $difault_lv = $mdl_user_lv->dump(array('default_lv' => 1), 'user_lv_id');
        if ($difault_lv) {
            $result1 = $mdl_user_lv->update(array('default_lv' => 0), array('user_lv_id' => $difault_lv['user_lv_id']));
            if ($result1) {
                $result = $mdl_user_lv->update(array('default_lv' => 1), array('user_lv_id' => $lv_id));
                $msg = ('默认会员等级设置成功');
            } else {
                $msg = ('默认会员等级设置失败');
            }
        } else {
            $result = $mdl_user_lv->update(array('default_lv' => 1), array('user_lv_id' => $lv_id));
            $msg = ('默认会员等级设置成功');
        }
        $this->end($result, $msg);
    }
}
