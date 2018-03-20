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

class universalform_ctl_admin_form extends desktop_controller {

    public function index() {
        $actions[] =  array(
            'label' => ('添加表单') ,
            'icon' => 'fa-plus',
            'href' => 'index.php?app=universalform&ctl=admin_form&act=edit',
        );
        $this->finder('universalform_mdl_form',array(
            'title' => '超级表单列表',
            'use_buildin_filter' => true,
            'actions' => $actions,
        ));
    }


    public function edit($form_id,$nav = 'basic') {
        if($form_id) {
            $mdl_form = $this->app->model('form');
            $form = $mdl_form->dump($form_id,'*','default');
            $this->pagedata['form'] = $form;
        }
        $this->pagedata['nav'] = $nav;
        $this->page('admin/form/edit.html');
    }

    //保存表单设置
    public function save() {
        $form = $_POST;
        $mdl_form = $this->app->model('form');
        $this->begin();
        if(!$mdl_form->save($form)) {
            $this->end(false,'操作失败');
        };
        $this->end(true,'操作成功',null,array(
            'form_id' => $form['form_id'],
        ));
    }

    //添加组件显示
    public function module_page($form_id){
        $this->pagedata['form_id'] = $form_id;
        $this->display('admin/form/module_page.html');
    }

    //编辑组件
    public function module_edit($module_id) {
        $mdl_form_module = $this->app->model('form_module');
        $module = $mdl_form_module->dump($module_id);
        if($module['options']){
            $module['options'] = implode(",",array_values($module['options']));
        }
        $this->pagedata['module'] = $module;
        $this->display('admin/form/module_edit.html');
    }

    //保存组件
    public function save_module(){
        $data = $_POST;
        $this->begin('index.php?app=universalform&ctl=admin_form&act=edit&p[0]='.$data['form_id'].'&p[1]=module');
        if($data['options'] == ''){
            unset($data['options']);
        }else{
            $data['options'] = explode(',',$data['options']);
            foreach ($data['options'] as $key => $value) {
                $data['options'][$value] = $value;
                unset($data['options'][$key]);
            }
        }
        $mdl_form_module = $this->app->model('form_module');
        if($mdl_form_module->save($data)){
            $this->end(true,('保存成功！'));
        }else{
            $this->end(false,('保存失败！'));
        }
    }

    //禁用组件
    public function hidden_module($module_id,$form_id) {
        $this->begin('index.php?app=universalform&ctl=admin_form&act=edit&p[0]='.$form_id.'&p[1]=module');
        if(!$module_id  || !$form_id) {
            $this->end(false,'未知表单组件');
        }
        $mdl_form_module = $this->app->model('form_module');
        if(!$mdl_form_module->update(array('show'=>'false'),array('module_id'=>$module_id))) {
            $this->end(false,'操作失败');
        };
        $this->end(true,'操作成功');
    }

    //启用组件
    public function show_module($module_id,$form_id) {
        $this->begin('index.php?app=universalform&ctl=admin_form&act=edit&p[0]='.$form_id.'&p[1]=module');
        if(!$module_id  || !$form_id) {
            $this->end(false,'未知表单组件');
        }
        $mdl_form_module = $this->app->model('form_module');
        if(!$mdl_form_module->update(array('show'=>'true'),array('module_id'=>$module_id))) {
            $this->end(false,'操作失败');
        };
        $this->end(true,'操作成功');
    }

    //删除组件
    public function remove($module_id) {
        $this->begin();
        if(!$module_id) {
            $this->end(false,'未知组件');
        }
        $mdl_form_module = $this->app->model('form_module');
        if(!$mdl_form_module->delete(array('module_id'=>$module_id))) {
            $this->end(false,'操作失败');
        };
        $this->end(true,'删除成功');
    }

    //快速修改排序
    public function quick_update() {
        $data = $_POST;
        $this->begin();
        if(!$data['module_id']) {
            $this->end(false,'未知表单组件');
        }
        $mdl_form_module = $this->app->model('form_module');
        if(!$mdl_form_module->save($data)) {
            $this->end(false,'操作失败');
        };
        $this->end(true,'操作成功');
    }







}


?>