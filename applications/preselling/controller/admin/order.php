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

class preselling_ctl_admin_order extends desktop_controller
{
    public function index($status) {

        $base_filter = array('status' => $status);
        $this->finder('preselling_mdl_orders',array(
            'title' => '预售单列表',
            'use_buildin_recycle' => true,
            'use_buildin_filter' => true,
            'base_filter' => $base_filter,
        ));
    }

    public function detail($presell_id) {
        $this->pagedata['order'] = $this->app->model('orders')->getRow('*',array('presell_id'=>$presell_id));
        return $this->display('admin/order/detail.html');
    }

    //退款申请
    public function refund() {
        $data = $_POST;
        $this->begin();
        $mdl_orders = app::get('preselling')->model('orders');
        if($data['deposit_pay_status'] == '1' && $order = $mdl_orders->getRow('*',array('presell_id'=>$data['presell_id']))) {
            if(!$order['deposit_bill_id']) {
                $this->end(false,'无定金支付单号');
            }
            $mdl_bills = app::get('ectools')->model('bills');
            //账单生产类
            $obj_bill = vmc::singleton('ectools_bill');
            $pay_bill = $mdl_bills->getRow('*',array('bill_id'=>$order['deposit_bill_id'],'bill_type'=>'payment','status|in'=>array('succ','progress')));
            //已支付生成退款单
            $bill_sdf = array(
                'order_id' => $order['presell_id'],
                'bill_type' => 'refund',
                'pay_object' => 'porder',
                'member_id' => $order['member_id'],
                'op_id' => $this->user->user_id,
                'status' => 'ready',
                'pay_mode' => 'online',
                'money' => $order['deposit_price'],
                'pay_app_id' => $order['pay_app'],
                'app_id' =>'preselling',
                'transaction_id' => $pay_bill['out_trade_no'],
                'memo' => '定金退款',
                'payment_bill_id' => $order['deposit_bill_id'],
            );
            if (!$obj_bill->generate($bill_sdf, $msg)) {
                $this->end(false, $msg);
            }
        }
        $this->end(true,'申请成功');
    }

    //保存备注
    public function save() {
        $data = $_POST;
        $this->begin();
        if(!$data['presell_id']) {
            $this->end(false,'未知预售单');
        }
        $mdl_orders = app::get('preselling')->model('orders');
        if(!$mdl_orders->save($data)) {
            $this->end(false,'操作失败');
        };
        $this->end(true,'操作成功');
    }

}