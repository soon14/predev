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

class supplier_ctl_admin_notice extends desktop_controller
{
    public function index()
    {
        if($this ->has_permission('supplier_notice_edit')){
            $actions[] = array(
                'label' => ('添加公告'),
                'icon' => 'fa-plus',
                'href' => 'index.php?app=supplier&ctl=admin_notice&act=edit',
            );
        }
        $this->finder('supplier_mdl_notice', array(
            'title' => ('公告列表'),
            'use_buildin_recycle'=>$this ->has_permission('supplier_notice_delete'),
            'use_buildin_set_tag'=>$this ->has_permission('supplier_notice_tag'),
            'actions' => $actions,
        ));
    }
    public function edit($notice_id)
    {
        $mdl_notice = $this->app->model('notice');
        if ($notice_id) {
            $notice = $mdl_notice->dump($notice_id);
            $this->pagedata['notice'] = $notice;
        }
        $this->page('admin/notice/edit.html');
    }

    public function save()
    {
        $this->begin('index.php?app=supplier&ctl=admin_notice&act=index');
        $notice = $_POST['notice'];
        if(!$notice['notice_id']){
            $notice['pubtime'] = time();
        }
        $mdl_notice = $this->app->model('notice');
        $this->end($mdl_notice->save($notice));
    }


}
