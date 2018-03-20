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

class preselling_ctl_admin_index extends desktop_controller
{
    public function index() {
        $group[] = array(
            'label' => ('预售权重') ,
            'data-submit' => 'index.php?app=preselling&ctl=admin_index&act=batch_edit&p[0]=worder',
            'data-target' => '_ACTION_MODAL_',
        );
        $custom_actions[] = array(
            'label' => '新建预售活动',
            'href' => 'index.php?app=preselling&ctl=admin_index&act=edit',
            'icon' => 'fa-plus',
            'target' =>'_ACTION_MODAL_'
        );
        $custom_actions[] = array(
            'label' => ('批量操作') ,
            'group' => $group,
        );
        $this->finder('preselling_mdl_activity',array(
            'title' => '预售活动',
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
                $this->pagedata['activity'] = $activity;
            };
        }
        $this->page('admin/edit.html');
    }

    public function save() {
        $this->begin('index.php?app=preselling&ctl=admin_index&act=index');
        $data = $_POST;
        $mdl_activity = $this->app->model('activity');
        if(!$data['conditions']) {
            $this->end(false,'选择参与预售商品');
        }
        if($data['deposit_starttime'] >= $data['deposit_endtime']) {
            $this->end(false,'订金支付开始时间 不可以大于等于 结束时间');
        }
        if($data['balance_starttime'] <= $data['deposit_starttime']) {
            $this->end(false,'尾款支付开始时间 不可以小于等于 定金支付时间');
        }
        if($data['balance_starttime'] >= $data['balance_endtime']) {
            $this->end(false,'尾款支付开始时间 不可以大于等于 结束时间');
        }
        if($data['balance_endtime'] <= $data['deposit_endtime']) {
            $this->end(false,'尾款支付结束时间 不可以小于等于 定金支付时间');
        }
        if($data['send_time'] <= $data['balance_endtime']) {
            $this->end(false,'预计发货时间 不可以小于 尾款支付结束时间');
        }

        if(!$this->dispose_data($data,$msg)) {
            $this->end(false,$msg);
        };

        if(!$mdl_activity->save($data)) {
            $this->end(false,'操作失败');
        };

        $this->end(true,'保存成功');
    }

    private function dispose_data(&$data,&$msg) {
        if(!$data['activity_id']) {
            $data['createtime'] = time();
        }
        if(!$data['product_id'] || !in_array($data['product_id'],$data['conditions'])) {
            $data['product_id'] = current($data['conditions'])['product_id'];
        }
        $data['member_lv_ids'] = empty($data['member_lv_ids']) ? null : implode(',', $data['member_lv_ids']);
        $data['deposit_starttime'] = strtotime($data['deposit_starttime']);
        $data['deposit_endtime'] = strtotime($data['deposit_endtime']);
        $data['balance_starttime'] = strtotime($data['balance_starttime']);
        $data['balance_endtime'] = strtotime($data['balance_endtime']);
        $data['send_time'] = strtotime($data['send_time']);
        $obj_math = vmc::singleton('ectools_math');
        foreach($data['conditions'] as &$condition) {
            if($condition['deposit'] > $condition['deposit_deduction'] ) {
                $msg = '定金金额不可以大于抵扣金额';
                return false;
            }
            if($condition['presell_price'] < $condition['deposit_deduction'] ) {
                $msg = '预售金额 不可以小于等于 定金抵扣金额';
                return false;
            }
            $condition['balance_payment'] = $obj_math->number_minus(array($condition['presell_price'],$condition['deposit_deduction']));
        }
        return true;
    }

    /*
     * 获取库存
     * */
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
            echo('<div class="alert alert-warning">您正在操作全部预售活动!</div>');
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