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


class couponactivity_ctl_admin_activity extends desktop_controller
{
    public function __construct(&$app) {
        parent::__construct($app);
    }

    public function index()
    {
        if ($this->has_permission('sales_activity_add')) {
            $custom_actions[] = array(
                'label' => ('活动添加') ,
                'icon'=>'fa-plus',
                'href' => 'index.php?app=couponactivity&ctl=admin_activity&act=add',
            );
        }
        // if ($this->has_permission('sales_activity')) {
        //     $custom_actions[] = array(
        //         'label' => ('批量操作') ,
        //         'group'=> array(
        //             [
        //                 'label' => ('向选中会员赠送优惠券') ,
        //                 'data-submit-result' => 'index.php?app=couponactivity&ctl=admin_activity&act=add',
        //                 'data-target' => '_ACTION_MODAL_',
        //            ],//['label' => '_SPLIT_']
        //         ),
        //     );
        // }

        $this->finder('couponactivity_mdl_activity', array(
            'title' => ('领券活动'),
            'use_buildin_filter' => true,
            'use_buildin_recycle' => false,
            'actions' => $custom_actions,
        ));
    }

    public function add()
    {
        if($_POST){
            $data = $_POST;
            $data['from_time'] = strtotime($data['from_time'])?:'';
            $data['to_time'] = strtotime($data['to_time'])?:'';
            $this->begin('index.php?app=couponactivity&ctl=admin_activity&act=index');
            $cpns = $data['cpns']['num'];
            if( is_array($cpns)&&!empty($cpns) ){
                unset($data['cpns']);
            }else{
                $this->end(false,'请至少选择一张优惠券');
            }
            if( !$data['from_time']||($data['to_time']&&$data['to_time']<=$data['from_time']) ){
                $this->end(false,'请选择正确的开始结束时间');
            }
            $mdl_activity = $this->app->model('activity');
            $mdl_activities = $this->app->model('activities');
            if($data['status']=='true'){
                $data['op_time'] = time();
            }
            $data['op_id'] = $this->user->user_id;
            $data['op_name'] = $this->user->user_data['name'];
            $data['createtime'] = time();
            if( $mdl_activity->save($data)&&$data['activity_id']>0 ){
                foreach($cpns as $k=>$v){
                    if($k<1){
                        $this->end(false,'优惠券有误');
                    }
                    if($v['num_sum']<$v['num']){
                        $this->end(false,'优惠券限领数量有误');
                    }
                    $activities = array(
                        'activity_id' => $data['activity_id'],
                        'cpns_id' => $k,
                        'num' => $v['num'],
                        'num_sum' => $v['num_sum']
                    );
                    if( !$mdl_activities->insert($activities) ){
                        $this->end(false,'保存失败');
                    }
                }
                $this->end(true,'保存成功');
            }
            $this->end(false,'保存失败');
        }else{
            $this->page('admin/activity/add.html');
        }
    }


    /**
     * 修改coupon.
     */
    public function edit($id)
    {
        $mdl_activity = $this->app->model('activity');
        $mdl_activities = $this->app->model('activities');
        if($_POST){
            $data = $_POST;
            $data['from_time'] = strtotime($data['from_time'])?:'';
            $data['to_time'] = strtotime($data['to_time'])?:'';
            $this->begin('index.php?app=couponactivity&ctl=admin_activity&act=index');
            $cpns = $data['cpns']['num'];
            $cpns_ids = $data['cpns']['cpns_id'];
            if( is_array($cpns)&&!empty($cpns) ){
                unset($data['cpns']);
            }else{
                $this->end(false,'请至少选择一张优惠券');
            }
            if( !$data['from_time']||!$data['to_time']||($data['to_time']&&$data['to_time']<=$data['from_time']) ){
                $this->end(false,'请选择正确的开始结束时间');
            }
            if($data['status']=='true'){
                $data['op_time'] = time();
            }
            $data['op_id'] = $this->user->user_id;
            $data['op_name'] = $this->user->user_data['name'];
            $data['createtime'] = time();
            if( $data['activity_id']>0&&$mdl_activity->update($data,['activity_id'=>$data['activity_id']])&&$mdl_activities->delete(['activity_id'=>$data['activity_id'],'cpns_id|notin'=>$cpns_ids]) ){
                foreach($cpns as $k=>$v){
                    if($k<1){
                        $this->end(false,'优惠券有误');
                    }
                    if($v['num_sum']<$v['num']){
                        $this->end(false,'优惠券限领数量有误');
                    }
                    $activities = array(
                        'activity_id' => $data['activity_id'],
                        'cpns_id' => $k,
                        'num' => $v['num'],
                        'num_sum' => $v['num_sum']
                    );
                    if($v['id']>0){
                        $mdl_achieve = $this->app->model('achieve');
                        $activities['id'] = $v['id'];
                        $num_sum = $mdl_achieve->getRow('sum(num) as num_sum',['activity_id' => $data['activity_id'],'cpns_id' => $k]);
                        $activities['achieve_sum'] = $num_sum['num_sum']?:0;
                    }
                    if( !$mdl_activities->save($activities) ){
                        $this->end(false,'保存失败');
                    }
                }
                $this->end(true,'保存成功');
            }
            $this->end(false,'保存失败');
        }else{
            if($id>0){
                $activity = $mdl_activity->getRow('*',['activity_id'=>$id]);
                if($activity['to_time']<1000000000){
                	$activity['to_time'] = '';
                }
                $cpns = $mdl_activities->getList('*',['activity_id'=>$activity['activity_id']]);
                $mdl_coupons = app::get('b2c')->model('coupons');
                $cpns_ids = array_keys(utils::array_change_key($cpns,'cpns_id'));
                $coupons = $mdl_coupons->getList('cpns_id,cpns_name,cpns_prefix',['cpns_id'=>$cpns_ids]);
                $coupons = utils::array_change_key($coupons,'cpns_id');
                $activity['cpns'] = $coupons;
                $activity['cpns_list'] = $cpns;
                $this->pagedata['data'] = $activity;
            }
            $this->page('admin/activity/edit.html');
        }
    }

    public function achieve(){
        $this->finder('couponactivity_mdl_achieve', array(
            'title' => ('领券活动记录'),
            'use_buildin_filter' => true,
            'use_buildin_recycle' => false,
            'actions' => $custom_actions,
        ));
    }

}
