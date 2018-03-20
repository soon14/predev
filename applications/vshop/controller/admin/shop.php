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

class vshop_ctl_admin_shop extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function index()
    {
        $this->finder('vshop_mdl_shop', array(
            'title' => ('店铺列表'),
//            'use_buildin_recycle' => true,
            'use_buildin_set_tag' => true,#是否启用标签
            // 'use_buildin_export' => true, #是否启用批量导出
            // 'use_buildin_import' => true, #是否启用批量导入
            'use_buildin_filter' => true, #是否启用筛选
            'actions' => array(
               array(
                   'label' => ('添加店铺'),
                   'icon' => 'fa-plus',
                   'href' => 'index.php?app=vshop&ctl=admin_shop&act=edit',
               ),
            ),
        ));
    }

    public function load($id)
    {
        $shop = $this->app->model('shop')->dump($id);
        $shop['image'] = base_storager::image_path($shop['gallery_default_image_id'], 's');
        $shop['region'] = vmc::singleton('vshop_view_helper')->modifier_regionpart($shop['region']);
        $this->splash('success', null, 'success', 'echo', array('vshop' => $shop));
    }

    public function edit($shop_id)
    {
        $mdl_shop = $this->app->model('shop');
        if ($shop_id) {
            $shop = $mdl_shop->dump($shop_id);
            $mdl_pam_members = app::get('pam')->model('members');
            $account = $mdl_pam_members->getRow('member_id,login_account',array('member_id'=>$shop['member_id']));
            $this->pagedata['shop_member_account'] = $account;
            $this->pagedata['shop'] = $shop;
        }
        $vshop_lvs = app::get('vshop')->model('lv')->getMLevel();
        foreach ($vshop_lvs as $k => $row) {
            $options[$row['shop_lv_id']] = $row['name'];
        }
        $this->pagedata['shop_lv_type']['options'] = $options;
        $this->pagedata['shop_lv_type']['value'] = $shop['shop_lv_id'];

        $this->page('admin/shop/edit.html');
    }

    public function save($redirect = false)
    {
        $data = $_POST;
        $mdl_shop = $this->app->model('shop');
        $shop = $data['shop'];
        if ($redirect) {
            $this->begin('');
        } else {
            $this->begin('index.php?app=vshop&ctl=admin_shop&act=edit&p[0]=' . $shop['shop_id']);
        }
        if (!isset($shop['shop_id'])) {
            $shop['shop_id'] = $mdl_shop->apply_id();
            $shop['createtime'] = time();
        } else {
            $old_shop = $mdl_shop->count(array('shop_id' => $shop['shop_id']));
            if (!$old_shop) {
                $this->end(false, '未知店铺信息');
            }
        }
        //vmc::dump($shop);
        if ($mdl_shop->save($shop)) {
            $this->end(true, '保存成功');
        } else {
            $this->end(false, '保存失败');
        }
    }

    // 会员查找
    public function member_search()
    {
        $account = $_POST['account'];
        $mdl_pam_members = app::get('pam')->model('members');
        $account_list = $mdl_pam_members->getList('member_id,login_account', array('login_account|head' => $account), 0, 5);
        echo json_encode($account_list);
    }
}
