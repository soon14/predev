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


class o2ocds_mdl_statement extends dbeav_model
{
    public $has_tag = true;
    public $has_many = array(
        'statement_index' => 'statement_index',
    );
    public $subSdf = array(
        'delete' => array(
            'statement_index' => array(
                '*',
            ) ,
        ),
    );
    public $defaultOrder = array(
        ' createtime',
        'DESC',
    );
    public function complete_bank(&$statement) {
        if(!$statement['payee'] && !$statement['payee_bank'] && !$statement['payee_account']) {
            if ($lately_statement = $this->getRow('payee,payee_bank,payee_account', array('relation_id' => $statement['relation_id'], 'relation_type' => $statement['relation_type']))) {
                $statement = array_merge($statement, $lately_statement);
                return true;
            };
            if ($statement['relation_type'] == 'store') {
                if ($store = $this->app->model('store')->getRow('account_name,bank,account', array('store_id' => $statement['relation_id']))) {
                    $statement['payee'] = $store['account_name'];
                    $statement['payee_bank'] = $store['bank'];
                    $statement['payee_account'] = $store['account'];
                };
                return true;
            } elseif ($statement['relation_type'] == 'enterprise') {
                if ($enterprise = $this->app->model('enterprise')->getRow('account_name,bank,account', array('enterprise_id' => $statement['relation_id']))) {
                    $statement['payee'] = $enterprise['account_name'];
                    $statement['payee_bank'] = $enterprise['bank'];
                    $statement['payee_account'] = $enterprise['account'];
                };
            }
        }
        return true;
    }

    public function apply_id()
    {
        $sign = '8';
        $tb = $this->table_name(1);
        do {
            $i = substr(mt_rand(), -3);
            $statement_id = $sign.date('ymdHis').$i;
            $row = $this->db->selectrow('SELECT statement_id from '.$tb.' where statement_id ='.$statement_id);
        } while ($row);

        return $statement_id;
    }
    public function modifier_status($col)
    {
        switch ($col) {
            case 'ready':
                return "<span class='label bg-blue'>待结算</span>";
            case 'noconfirm':
                return "<span class='label label-danger'>待确认</span>";
            case 'process':
                return "<span class='label bg-yellow'>处理中</span>";
            case 'succ':
                return "<span class='label label-success'>已结算</span>";
            case 'cancel':
                return "<span class='label label-default'>已取消</span>";
        }
    }
    public function modifier_op_id($col)
    {
        $mdl_desktop_user = app::get('desktop')->model('users');
        $user = $mdl_desktop_user->dump($col);

        return $user['name'];
    }
    public function pre_recycle($rows)
    {
        $this->recycle_msg = '删除成功!';
        $statement_id_arr = array_keys(utils::array_change_key($rows, 'statement_id'));
        if ($this->count(array('status' => 'succ', 'statement_id' => $statement_id_arr))) {
            $this->recycle_msg = '无法删除已完成结算单据!';

            return false;
        }
        //操作日志
        $obj_log = vmc::singleton('o2ocds_operatorlog');
        if(method_exists($obj_log,'statement_log')) {
            $obj_log->statement_log($rows,'delete');
        }

        return true;
    }
}
