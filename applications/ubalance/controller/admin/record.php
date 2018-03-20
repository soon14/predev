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

class ubalance_ctl_admin_record extends desktop_controller
{

    public function index()
    {
        $this->finder('ubalance_mdl_fundlog', array(
            'title' => ('流水列表'),
            'use_buildin_filter' => true,
            'use_buildin_export' => $this ->has_permission('ubalance_fundlog_export'),
            'use_view_tab' => true
        ));
    }

    public function count()
    {
        $to = $_POST['t'] ?strtotime($_POST['t']) :strtotime(date('Y-m-d'));
        $from = $_POST['f'] ?strtotime($_POST['f']) : strtotime('-1 week', $to);
        $this ->pagedata['from'] = $from;
        $this ->pagedata['to'] = $to;
        $sql_basic = "SELECT SUM(change_fund) as amount FROM vmc_ubalance_fundlog";
        $sql_all = "SELECT SUM(change_fund) as amount FROM vmc_ubalance_fundlog where 1 "
            .($from ? 'AND opt_time>'.$from :'') .($to ? ' AND opt_time<='.$to :'');
        $sql_recharge = $sql_all." AND type IN(1,8)";
        $sql_income = $sql_all." AND type='4'";
        $mdl_bills = app::get('ectools') ->model('bills');
        $pay_by_balance = $mdl_bills ->count(array('pay_app_id' =>'balance' ,'succ' =>'status' ,'bill_type'=>'payment','pay_object'=>'order'));
        $all = $mdl_bills ->count(array('succ' =>'status' ,'bill_type'=>'payment','pay_object'=>'order'));
        $rat =0;
        if($all){
            $rat  = $pay_by_balance/$all;
        }
        $db = vmc::database();
        $this ->pagedata['all'] =$db->selectrow($sql_basic);
        $this ->pagedata['recharge'] =$db->selectrow($sql_recharge);
        $this ->pagedata['income'] =$db->selectrow($sql_income);
        $this ->pagedata['rat'] =number_format($rat*100,2).'%';
        //快捷
        $this->pagedata['tody'] = date('Y-m-d H:i', strtotime(date('Y-m-d')));
        $this->pagedata['from_arr'] = array(
            'w' => date('Y-m-d H:i', strtotime('-1 week', strtotime($this->pagedata['tody']))),
            'm' => date('Y-m-d H:i', strtotime('-1 month', strtotime($this->pagedata['tody']))),
            'q' => date('Y-m-d H:i', strtotime('-3 month', strtotime($this->pagedata['tody']))),
        );
        $this->page('admin/record/count.html');
    }

}
