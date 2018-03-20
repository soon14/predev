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


class o2ocds_ctl_mobile_passport extends mobile_controller {

    public function __construct($app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $user_obj = vmc::singleton('b2c_user_object');
        $this->app->member_id = $user_obj->get_member_id();
    }

    /*
     * 企业入口
     * */
    public function enterprise() {
        $user_obj = vmc::singleton('b2c_user_object');
        $pam_data = $user_obj->get_pam_data('*', $this->app->member_id);
        if(!$pam_data['mobile']) {
            $this->splash('error', null, '未绑定手机号码');
        }
        if($this->app->model('store')->getRow('store',array('member_id'=>$this->app->member_id))) {
            $this->splash('error', null, '已开通店铺');
        };
        if($this->app->model('enterprise')->getRow('store',array('member_id'=>$this->app->member_id))) {
            $this->splash('error', null, '已开通企业');
        };
        $this->page('mobile/default.html');
    }

    /*
     * 新增企业操作
     * */
    public function create_enterprise() {
        $enterprise = utils::_filter_input($_POST);
        $enterprise['member_id'] = $enterprise['member_id']?$enterprise['member_id']:$this->app->member_id;
        if($have_enterprise = $this->app->model('relation')->getRow('*',array('member_id'=>$this->app->member_id))) {
            if($have_enterprise['relation'] == 'admin') {
                $this->splash('error',null,'已是企业身份');
            }else{
                $this->splash('error',null,'已有其他身份');
            }
        }
        $db = vmc::database();
        $this->transaction_status = $db->beginTransaction();

        $mdl_enterprise = $this->app->model('enterprise');
        if(!$mdl_enterprise->check($enterprise,$msg)) {
            $this->splash('error', null, $msg);
        };
        if(!$mdl_enterprise->save($enterprise)) {
            $this->splash('error', null, '保存失败');
        }else{
            foreach(vmc::serviceList('enterprise.create_after') as $obj) {
                if(!$obj->exec($enterprise,$msg)) {
                    $db->rollback();
                    $this->splash('error', null, $msg);
                };
            }
        }
        $db->commit($this->transaction_status);
        $this->splash('success', null, '保存成功');

    }

    /*
    * 新增店铺操作
    * */
    public function create_store() {
        $store = utils::_filter_input($_POST);
        $store['member_id'] = $store['member_id']?$store['member_id']:$this->app->member_id;
        if($this->app->model('relation')->getRow('*',array('member_id'=>$this->app->member_id,'relation|notin'=>'store'))) {
            $this->splash('error',null,'已有其他身份');
        }
        $store['status'] = '1';
        $mdl_store = $this->app->model('store');
        if($store['images']) {
            $images = array();
            foreach ((array) $store['images'] as $imageId) {
                $images[] = array(
                    'target_type' => 'o2ocds_store',
                    'image_id' => $imageId,
                );
            }
            $store['images'] = $images;
            unset($images);
        }
        if(!$mdl_store->check($store,$msg)) {
            $this->splash('error', null, $msg);
        };
        $db = vmc::database();
        $this->transaction_status = $db->beginTransaction();

        if(!$mdl_store->save($store) ) {
            $db->rollback();
            $this->splash('error', null, '保存失败');
        }else{
            foreach(vmc::serviceList('store.create_after') as $obj) {
                if(!$obj->exec($store,$msg)) {
                    $db->rollback();
                    $this->splash('error', null, $msg);
                };
            }
        }

        $db->commit($this->transaction_status);
        $this->splash('success', null, '保存成功');

    }

    /*
     * 检查二维码信息
     * */
    public function check_qrcode($qrcode) {
        $mdl_qrcode = $this->app->model('qrcode');

        if(!$qrcode_id = $mdl_qrcode->get_qrcode_id($qrcode,$msg)) {
            $this->splash('error',null,'未知二维码');
        };
        if(!$qrcode_data = $mdl_qrcode->getRow('*',array('qrcode_id'=>$qrcode_id))) {
            $this->splash('error',null,'未知二维码');
        };
        if(!$qrcode_data['store_id']) {
            $this->splash('error',null,'二维码还未初始化');
        };
        $mdl_store = $this->app->model('store');
        $store = $mdl_store->dump($qrcode_data['store_id']);
        $show_data = array(
            'qrcode'=>$qrcode_data['prefix'].$qrcode_data['qrcode'],
            'store_name'=>$store['name'],
            'store_sno'=>$store['sno'],
        );
        $this->splash('success',null,$show_data);
    }

    /*
     * 核销服务码
     * */
    public function cancel_service_code() {
        $data = utils::_filter_input($_POST);
        if(!$data['service_code']) {
            $this->splash('error',null,'未知服务码');
        }
        $data['service_code'] = strtoupper($data['service_code']);
        /*if(!$data['order_id']) {
            $this->splash('error',null,'未知订单');
        }*/
        $mdl_service_code = $this->app->model('service_code');
        if(!$service_code = $mdl_service_code->getRow('*',array('service_code'=>$data['service_code']))) {
            $this->splash('error',null,'未知服务码');
        };
        if($service_code['member_id']) {
            $this->splash('error',null,'已核销');
        }
        $service_code['member_id'] = $this->app->member_id;
        $service_code['cancel_time'] = time();
        $service_code['status'] = '1';
        $db = vmc::database();
        $this->transaction_status = $db->beginTransaction();

        if(!$mdl_service_code->save($service_code)) {
            $db->rollback();
            $this->splash('error',null,'核销失败');
        };
        if($service_code['integral'] != 0) {
            $integral_change_data = array(
                'member_id'=>$service_code['member_id'],
                'change'=>$service_code['integral'],
                'change_reason'=>'order',
                'op_model'=>'member',
                'op_id'=>$service_code['member_id'],
                'remark'=>'服务码核销:'.$service_code['service_code'],
            );
            if(!vmc::singleton('b2c_member_integral')->change($integral_change_data,$msg)){
                $db->rollback();
                $this->splash('error',null,'服务码核销失败'.$msg);
            }
        }
        $db->commit($this->transaction_status);
        $this->splash('success',null,array('integral'=>$service_code['integral']));

    }


    /*
     * 绑定关系 店铺和店员/企业和业务员
     * */
    public function bind_relation($confirm = false) {
        $endata = $_POST['relation_encode'];
        $relation_data = utils::decrypt($endata);
        if(!$relation_data['relation_id'] || !$relation_data['relation']) {
            $this->splash('error',null,'未知身份');
        }
        $mdl_relation = $this->app->model('relation');
        if(!$relation_data = $mdl_relation->getRow('*',$relation_data)) {
            $this->splash('error',null,'未知身份');
        }
        if(!$confirm){
            $mdl_biz = $this->app->model($relation_data['type']);
            if(!$mdl_biz){
                $this->splash('error',null,'异常邀请');
            }
            $biz = $mdl_biz->dump($relation_data['relation_id']);
            if(!$biz){
                $this->splash('error',null,'异常邀请');
            }
            $this->pagedata['relation_data'] = $relation_data;
            $this->pagedata['biz'] = $biz;
            $this->page('mobile/default.html');
            return;
        }
        if($this->app->member_id == $relation_data['member_id']) {
            $this->splash('error',null,'不能邀请自己');
        }
        $relation = array(
            'member_id' => $this->app->member_id,
            'type' => $relation_data['type'],
            'relation_id' => $relation_data['relation_id'],
        );
        // if($mdl_relation->count(array('relation_id'=>$relation_data['relation_id'],'member_id'=>$this->app->member_id))){
        //     $this->splash('error',null,'您已存在其他身份');
        // }
        if($relation_data['relation'] == 'admin') {
            $relation['relation'] = 'salesman';
        }elseif($relation_data['relation'] == 'manager') {
            $relation['relation'] = 'salesclerk';
        }
        if($mdl_relation->getRow('*',$relation)) {
            $this->splash('error',null,'已经绑定过');
        }
        if($mdl_relation->getRow('*',array('member_id'=>$this->app->member_id,'relation|notin'=>$relation_data['type']))) {
            $this->splash('error',null,'已有其他身份');
        }
        $relation['time'] = time();
        if(!$mdl_relation->save($relation)) {
            $this->splash('error',null,'加入失败');
        };

        $this->splash('success',null,'加入成功');
    }


}
