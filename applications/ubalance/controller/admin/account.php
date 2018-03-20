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

class ubalance_ctl_admin_account extends desktop_controller
{

    public function index()
    {
        if ($this->has_permission('ubalance_recharge')) {
            $group[] = array(
                'label' => ('为全部账户充值') ,
                'data-submit-result' => 'index.php?app=ubalance&ctl=admin_account&act=batch_edit&p[0]=all',
                'data-target' => '_ACTION_MODAL_',
            );
            $group[] = array(
                'label' => ('为选中账户充值') ,
                'data-submit' => 'index.php?app=ubalance&ctl=admin_account&act=batch_edit&p[0]=select',
                'data-target' => '_ACTION_MODAL_',
            );
            $group[] = array(
                'label' => ('为当前筛选账户充值') ,
                'data-submit-result' => 'index.php?app=ubalance&ctl=admin_account&act=batch_edit&p[0]=filter',
                'data-target' => '_ACTION_MODAL_',
            );
            $actions[] = array(
                'label' => '账户充值',
                'group' => $group,
            );
        }
        $this->finder('ubalance_mdl_account', array(
            'title' => ('账户列表'),
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'use_buildin_export' => $this ->has_permission('ubalance_account_export'),
            'actions' => $actions
        ));
    }

    public function save()
    {
        $this->begin('index.php?app=ubalance&ctl=admin_account&act=index');
        $data = $_POST;
        $data['last_modify'] = time();
        if (!app::get('ubalance')->model('account')->save($data)) {
            $this->splash('error', '', '操作失败');
        }
        $this->end(true, '操作成功');
    }

    public function edit()
    {
        $member_id = $_GET['member_id'];
        $mdl_account = app::get('ubalance')->model('account');
        $account = $mdl_account->getRow('*', array('member_id' => $member_id));
        $this->pagedata['account'] = $account;
        $this->display('admin/account/edit.html');
    }

    public function batch_edit($type) {
        $filter = $_POST;
        $this->pagedata['type'] = $type;
        $setting = app::get('ubalance')->model('set')->getRow('*');
        $this->pagedata['setting'] = $setting;
        $this->pagedata['filter'] = htmlspecialchars(serialize($filter));
        $this->display('admin/account/batchedit.html');
    }

    public function batch_save() {
        $this->begin();
        $filter = unserialize($_POST['filter']);
        //金额判断
        $balance_set = app::get('ubalance')->model('set')->getRow('*');
        if ($_POST['money'] < $balance_set['recharge_limit']['limit_minimum'] || $_POST['money'] > $balance_set['recharge_limit']['limit_maximum']) {
            $this->end(false,'充值金额不在范围内');
        }
        $mdl_account = app::get('ubalance')->model('account');
        switch($_POST['type']) {
            case 'all':
                $member_ids = $mdl_account->getList('member_id');
                $member_ids = array_keys(utils::array_change_key($member_ids,'member_id'));
                break;
            case 'select':
                $member_ids = $filter['member_id'];
                break;
            case 'filter':
                $member_ids = $mdl_account->getList('member_id',$filter);
                $member_ids = array_keys(utils::array_change_key($member_ids,'member_id'));
                break;
        }
        $params = array(
            'member_ids' => $member_ids,
            'ubalance_name' => $balance_set['name'],
            'money' => $_POST['money'],
            'op_id' => $this->user->user_id,
            'memo' => $_POST['memo'],
        );
        if(count($member_ids) > 1000) {
            system_queue::instance()->publish('ubalance_tasks_batchrecharge', 'ubalance_tasks_batchrecharge', $params);
            $this->end(true,'充值会员过多，已加入队列执行');
        };
        $obj_bill = vmc::singleton('ectools_bill');
        $mdl_bills = app::get('ectools')->model('bills');
        $error_member_ids = array();
        foreach($member_ids as $member_id) {
            $bill_sdf = array(
                'subject' => $balance_set['name'].'充值',
                'bill_type' => 'payment',
                'pay_mode' => 'online',
                'app_id' => 'ubalance',
                'pay_object' => 'recharge',
                'money' => (float) $_POST['money'],
                'member_id' => $member_id,
                'status' => 'succ',
                'pay_app_id' => 'offline',
                'op_id' => $this->user->user_id,
                'pay_fee' => null,
                'memo' => $_POST['memo'],
            );
            if(!$bill_sdf['bill_id']) {
                $bill_sdf['bill_id'] = $mdl_bills->apply_id($bill_sdf);
            }
            if (!$obj_bill->generate($bill_sdf, $msg)) {
                $error_member_ids[] = $member_id;
            }
        }
        if($error_member_ids) {
            $error_msg = "充值失败会员ID：".implode(',',$error_member_ids);
            logger::error($error_msg);
        }

        //操作日志
        if ($obj_operatorlogs = vmc::service('operatorlog.members')) {
            $obj_operatorlogs = vmc::singleton('ubalance_operatorlog_account');
            $obj_operatorlogs->batch_save_log($params);
        }

        $this->end(true,$error_msg?$error_msg:'充值成功');
    }

}
