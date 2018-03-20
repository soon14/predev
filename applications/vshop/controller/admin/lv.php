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


class vshop_ctl_admin_lv extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
        // header("cache-control: no-store, no-cache, must-revalidate");
    }

    public function index()
    {
        if ($this->has_permission('vshoplv_edit')) {
            $actions = array(
                array(
                    'icon' => 'fa-plus',
                    'label' => ('添加店铺等级'),
                    'href' => 'index.php?app=vshop&ctl=admin_lv&act=edit',
                 ),
            );
        }

        if ($this->has_permission('vshoplv_del')) {
            $use_buildin_recycle = true;
        }

        $this->finder('vshop_mdl_lv', array(
            'title' => ('微店铺等级'),
            'use_buildin_recycle' => $use_buildin_recycle,
            'actions' => $actions,
        ));
    }

    public function edit($shop_lv_id = null)
    {
        if ($shop_lv_id != null) {
            $mem_lv = $this->app->model('lv');
            $aLv = $mem_lv->dump($shop_lv_id);
            $aLv['default_lv_options'] = array('1' => ('是'), '0' => ('否'));
            $this->pagedata['lv'] = $aLv;
        }

        $this->page('admin/shop/lv.html');
    }

    public function save()
    {
        $end_go = vmc::router()->gen_url(array('app' => 'vshop', 'ctl' => 'admin_lv', 'act' => 'index'));
        $this->begin($end_go);
        $objMemLv = $this->app->model('lv');
        if ($objMemLv->validate($_POST, $msg)) {
            if ($_POST['shop_lv_id']) {
                $olddata = app::get('vshop')->model('lv')->dump($_POST['shop_lv_id']);
            }
            #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
            if ($objMemLv->save($_POST)) {
                #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
                if ($obj_operatorlogs = vmc::service('operatorlog.shop')) {
                    if (method_exists($obj_operatorlogs, 'vshop_lv_log')) {
                        $newdata = app::get('vshop')->model('lv')->dump($_POST['shop_lv_id']);
                        $obj_operatorlogs->vshop_lv_log($newdata, $olddata);
                    }
                }
                #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
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
        $end_go = vmc::router()->gen_url(array('app' => 'vshop', 'ctl' => 'admin_lv', 'act' => 'index'));
        $this->begin($end_go);
        $objMemLv = $this->app->model('lv');
        $difault_lv = $objMemLv->dump(array('default_lv' => 1), 'shop_lv_id');
        if ($difault_lv) {
            $result1 = $objMemLv->update(array('default_lv' => 0), array('shop_lv_id' => $difault_lv['shop_lv_id']));
            if ($result1) {
                $result = $objMemLv->update(array('default_lv' => 1), array('shop_lv_id' => $lv_id));
                $msg = ('默认店铺等级设置成功');
            } else {
                $msg = ('默认店铺等级设置失败');
            }
        } else {
            $result = $objMemLv->update(array('default_lv' => 1), array('shop_lv_id' => $lv_id));
            $msg = ('默认店铺等级设置成功');
        }
        $this->end($result, $msg);
    }
}
