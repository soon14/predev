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

class groupbooking_ctl_admin_index extends desktop_controller
{
    public function index() {
        $group[] = array(
            'label' => ('拼团权重') ,
            'data-submit' => 'index.php?app=groupbooking&ctl=admin_index&act=batch_edit&p[0]=worder',
            'data-target' => '_ACTION_MODAL_',
        );
        $custom_actions[] = array(
            'label' => '新建拼团活动',
            'href' => 'index.php?app=groupbooking&ctl=admin_index&act=edit',
            'icon' => 'fa-plus',
            'target' =>'_ACTION_MODAL_'
        );
        $custom_actions[] = array(
            'label' => ('批量操作') ,
            'group' => $group,
        );
        $this->finder('groupbooking_mdl_activity',array(
            'title' => '多人拼团',
            'use_buildin_recycle' => true,
            'use_buildin_filter' => true,
            'actions' => $custom_actions
        ));
    }

    public function edit($activity_id) {
        //////////////////////////// 会员等级 //////////////////////////////
        $mMemberLevel = app::get('b2c')->model('member_lv');
        $this->pagedata['member_level'] = $mMemberLevel->getList('member_lv_id,name', array(), 0, -1, 'member_lv_id ASC');
        if($activity_id) {
            $mdl_activity = $this->app->model('activity');
            if($activity = $mdl_activity->getRow('*',array('activity_id'=>$activity_id))) {
                $activity['member_lv_ids'] = empty($activity['member_lv_ids']) ? null : explode(',', $activity['member_lv_ids']);
                $this->pagedata['is_order'] = app::get('groupbooking')->model('orders')->getRow('gb_id',array('activity_id' => $activity['activity_id']));
                $this->pagedata['activity'] = $activity;
            };
        }
        $this->page('admin/edit.html');
    }

    public function save() {
        $this->begin('index.php?app=groupbooking&ctl=admin_index&act=index');
        $data = $_POST;
        $mdl_activity = $this->app->model('activity');

        if(!$data['conditions']) {
            $this->end(false,'选择参与拼团商品');
        }
        if($data['people_number'] < 1 && !$data['activity_id']) {
            $this->end(false,'参团人数必须大于1人');
        }elseif($data['people_number'] && $data['activity_id']) {
            $people_number = $mdl_activity->getRow('people_number',array('activity_id'=>$data['activity_id']));
            if($people_number['people_number'] != $data['people_number'] && app::get('groupbooking')->model('orders')->getRow('gb_id',array('activity_id' => $data['activity_id'])) > 0 ) {
                $this->end(false,'已有人参与活动，不可修改参团人数');
            }
        }
        if(!$data['activity_id']) {
            $data['createtime'] = time();
        }
        if(!$data['product_id'] || !in_array($data['product_id'],array_keys($data['conditions']))) {
           $data['product_id'] = current($data['conditions'])['product_id'];
        }
        $data['member_lv_ids'] = empty($data['member_lv_ids']) ? null : implode(',', $data['member_lv_ids']);
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        if($data['start_time'] >= $data['end_time']) {
            $this->end(false,'活动开始时间不可以大于等于结束时间');
        }
        if(!$mdl_activity->save($data)) {
            $this->end(false,'操作失败');
        };

        $this->end(true,'保存成功');
    }

    public function get_stock() {
        $this->begin();
        if(!$_POST['product_id']) {
            $this->end(false,'未知参数');
        }
        if(!$bn = app::get('b2c')->model('products')->getRow('bn',array('product_id'=>$_POST['product_id']))) {
            $this->end(false,'未知商品');
        };
        if(!$stock = app::get('b2c')->model('stock')->getRow('*',array('sku_bn'=>$bn['bn']))) {
            $this->end(false,'未知库存');
        };
        $this->end(true,'成功','',$stock);
    }

    /**
     * 批量编辑.
     */
    public function batch_edit($type = '')
    {
        $filter = $_POST;
        if(empty($filter)){
            echo('<div class="alert alert-warning">您正在操作全部拼团活动!</div>');
            exit;
        }
        switch ($type) {
            case 'worder':
                break;

        }
        $this->pagedata['filter'] = htmlspecialchars(serialize($filter));
        $this->display('admin/batchedit/'.$type.'.html');
    }

    public function batch_save(){
        $this->begin();
        $params = $_POST;
        $type = $params['type'];
        $filter = unserialize(trim($params['filter']));
        $mdl_activity = $this->app->model('activity');
        switch ($type) {
            case 'worder':
                if(!$mdl_activity->update($params['set'],$filter)){
                    $this->end(false,'保存失败');
                }
                $this->end(true,'保存成功');
                break;

        }

    }


}