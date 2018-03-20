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
class marketing_ctl_admin_group extends desktop_controller{


    public function index(){
        $this ->finder('marketing_mdl_group' ,array(
            'title' =>'会员分组',
            'actions' =>array(
                array(
                    'label' =>'新建分组',
                    'href' =>'index.php?app=marketing&ctl=admin_group&act=edit_group'
                )
            )
        ));
    }

    public function edit_group($group_id){
        if($group_id){
            $group =$this ->app ->model('group') ->dump($group_id);
            $this ->pagedata['group'] = $group;
            if($group['conditions']['group_id']){
                $this ->pagedata['extend_group'] = $this ->app ->model('group')->getList('*' ,array('group_id'=>$group['conditions']['group_id']));
            }
        }
        $this ->pagedata['condition'] = vmc::singleton('marketing_condition')->get_conditions();
        $this ->pagedata['areas'] =app::get('ectools')->model('regions')->getList('region_id,local_name', array(
            'region_grade' => 1
        ));
        $this ->pagedata['base_filter'] =array('status'=>'1');
        $this->page('admin/group/edit.html');

    }

    public function condition(){
        $name = $_POST['condition'];
        $condition = vmc::singleton('marketing_condition')->get_conditions($name);
        $this ->pagedata['name'] = $name;
        $this ->pagedata['condition'] = $condition;
        $this ->pagedata['position'] = $_POST['position'];
        $this ->pagedata['type'] = $_POST['type'];
        $this ->display('admin/group/condition.html');
    }

    public function save(){
        $this ->begin();
        $_POST['from_time'] = strtotime($_POST['from_time']);
        $_POST['to_time'] = strtotime($_POST['to_time']);
        $create_member = true;
        if($_POST['group_id']){
            $group = $this ->app->model('group')->dump($_POST['group_id']);
            if($_POST['conditions'] == $group['conditions']){
                $create_member = false;
            }
        }
        if(!$this ->app->model('group')->save($_POST)){
            $this ->end(false ,'保存失败');
        }
        if($create_member){
            //TODO 分析相关会员
            system_queue::instance()->publish('marketing_tasks_members', 'marketing_tasks_members', $_POST);
        }
        $this ->end(true ,'保存成功');
    }

    public function ajax_group(){
        $this ->pagedata['extend_group'] = $this ->app ->model('group') ->getList('*' ,$_POST);
        $this ->display('admin/group/ajax_group.html');
    }

    public function count($group_id){
        $group = $this ->app->model('group')->dump($group_id);
        $group['status'] = '0';
        if(!$this ->app->model('group')->save($group)){
            $this ->splash('error' ,'','数据更新失败');
        }
        system_queue::instance()->publish('marketing_tasks_members', 'marketing_tasks_members', $group);
        $this ->splash('success' ,'','已进入队列执行，请稍候查看');
    }
}