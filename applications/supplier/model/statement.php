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


class supplier_mdl_statement extends dbeav_model
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
            case 'process':
                # code...
                return "<span class='label label-warning'>处理中</span>";
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

        return true;
    }
}
