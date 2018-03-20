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

class registercoupon_ctl_admin_rule extends desktop_controller
{
    public function index()
    {
        $this->finder("registercoupon_mdl_rule", array(
            'title' => ('注册营销'),
            'use_buildin_recycle' => true,
            'actions' => array(
                array(
                    'label' => ('添加'),
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=registercoupon&ctl=admin_rule&act=create',
                ),
            )
        ));
    }

    public function create()
    {
        $this ->pagedata['coupons'] = app::get('b2c') ->model('coupons') ->getList('*' ,array('cpns_status' =>'1' ,'cpns_type' => '1'));
        $this->page('admin/rule/detail.html');
    }

    public function save()
    {
        $this->begin('index.php?app=registercoupon&ctl=admin_rule&act=index');
        $mdl_rule = $this->app->model('rule');
        try {
            $mdl_rule->save_update($_POST);
        } catch (Exception $e) {
            $this->end(false, $e->getMessage());
        }
        $this->end(true, "信息保存成功");
    }

    public function edit($rule_id)
    {
        $mdl_rule = $this->app->model('rule');
        $rule = $mdl_rule->dump($rule_id);
        $this->pagedata['rule_info'] = $rule;
        $this ->pagedata['coupons'] = app::get('b2c') ->model('coupons') ->getList('*' ,array('cpns_status' =>'1' ,'cpns_type' => '1'));
        $this->page('admin/rule/detail.html');
    }
}