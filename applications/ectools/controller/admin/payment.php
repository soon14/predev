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


class ectools_ctl_admin_payment extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function index()
    {
        if($this->has_permission('bill_export_payment')) {
            $use_buildin_export = true;
        }
        $this->finder('ectools_mdl_bills', array(
            'title' => '应收款',
            'use_buildin_recycle' => false,
            'use_buildin_export' => $use_buildin_export,
            'use_buildin_filter' => true,
            'base_filter' => array('bill_type' => 'payment'),
        ));
    }
    public function save()
    {
        $this->begin();
        $mdl_bills = $this->app->model('bills');
        if (!$_POST['bill_id']) {
            $this->end(false);
        }
        $bill = $mdl_bills->dump($_POST['bill_id']);
        if ($_POST['status'] && $bill['status'] != $_POST['status'] && ($bill['status'] != 'ready' || $bill['status'] != 'progress')) {
            $bill['status'] = $_POST['status'];
            $bill['payer_account'] = $_POST['payer_account'];
            $bill['payer_bank'] = $_POST['payer_bank'];
            $bill['out_trade_no'] = $_POST['out_trade_no'];
            $bill['memo'] = $_POST['memo'];
            $bill['pay_app_id'] = $_POST['pay_app_id'];//手动即线下
            $flag = vmc::singleton('ectools_bill')->generate($bill, $msg);
            $this->end($flag, $msg);
        } else {
            $up_data['payer_account'] = $_POST['payer_account'];
            $up_data['payer_bank'] = $_POST['payer_bank'];
            $up_data['out_trade_no'] = $_POST['out_trade_no'];
            $up_data['memo'] = $_POST['memo'];
            $flag = $mdl_bills->update($up_data, array('bill_id' => $_POST['bill_id']));
            $this->end($flag);
        }
    }

    /** 新建支付订单
     * @params array - 订单详细内容
     *
     * @return bool - 订单成功与否
     */
    public function addnew($arrPayments = array())
    {
        echo __FILE__.':'.__LINE__;
    }
}
