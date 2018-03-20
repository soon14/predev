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

class supplier_ctl_admin_supplier extends desktop_controller
{
    public function index()
    {
        if($this ->has_permission('supplier_edit')){
            $actions[] = array(
                'label' => ('添加供应商'),
                'icon' => 'fa-plus',
                'href' => 'index.php?app=supplier&ctl=admin_supplier&act=edit',
            );
        }
        $this->finder('supplier_mdl_supplier', array(
            'title' => ('供应商列表'),
            // 'use_buildin_recycle'=>true,
            'use_buildin_set_tag'=>$this ->has_permission('supplier_tag'),
            'actions' => $actions,
        ));
    }
    public function edit($supplier_id)
    {
        $mdl_supplier = $this->app->model('supplier');
        if ($supplier_id) {
            $supplier = $mdl_supplier->dump($supplier_id);
            $mdl_pam_members = app::get('pam')->model('members');
            $account = $mdl_pam_members->getRow('member_id,login_account',array('member_id'=>$supplier['member_id']));
            $this->pagedata['supplier_member_account'] = $account;
        }
        $this->pagedata['supplier'] = $supplier;
        $this->page('admin/supplier/edit.html');
    }

    public function save()
    {
        $this->begin('index.php?app=supplier&ctl=admin_supplier&act=index');
        $supplier = $_POST['supplier'];
        $mdl_supplier = $this->app->model('supplier');
        if(!$supplier['supplier_id'] && $mdl_supplier->count(array('supplier_name'=>$supplier['supplier_name']))){
            $this->end(false,'重复的供应商名称');
        }
        if(!$supplier['supplier_id'] && $mdl_supplier->count(array('supplier_bn'=>$supplier['supplier_bn']))){
            $this->end(false,'重复的供应商编号');
        }
        if(empty($supplier['member_id'])){
            $supplier['member_id'] = 0;
        }
        if(empty($supplier['dlyplace_send'])){
            $supplier['dlyplace_send'] = 0;
        }
        if(empty($supplier['dlyplace_reship'])){
            $supplier['dlyplace_reship'] = 0;
        }
        $this->end($mdl_supplier->save($supplier));
    }

    public function member_search(){
        $account = $_POST['account'];
        $mdl_pam_members = app::get('pam')->model('members');
        $account_list = $mdl_pam_members->getList('member_id,login_account',array('login_account|head'=>$account),0,5);
        echo json_encode($account_list);
    }
}
