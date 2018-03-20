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


class vshop_mdl_voucher extends dbeav_model
{
    public $has_tag = true;
    public $has_many = array(
        'items' => 'voucher_items',
    );
    public $subSdf = array(
        'delete' => array(
            'items' => array(
                '*',
            ) ,
        ),
    );
    public $defaultOrder = array(
        ' createtime',
        'DESC',
    );

    public function count_subprice($shop_id, $from = false, $to = false,$status = false)
    {
        $mdl_voucher_items = $this->app->model('voucher_items');
        $where_str = '1';
        if ($from) {
            if(!is_numeric($from)){
                $form = strtotime($from);
            }
            $where_str .= ' AND v.createtime >='.$from;
        }
        if ($to) {
            if(!is_numeric($to)){
                $to = strtotime($to);
            }
            $where_str .= ' AND v.createtime <='.$to;
        }
        if ($status){
            $where_str.= ' AND v.status = "'.$status.'"';
        }
        if($shop_id){
            if(is_array($shop_id)){
                $where_str .= ' AND v.shop_id IN '."('" . implode("','", $shop_id) . "')";
            }else{
                $where_str .= ' AND v.shop_id = '.$shop_id;
            }
        }
        $count_sql = 'select sum(vi.s_subprice) as total from `'.$mdl_voucher_items->table_name(1)
            .'` AS vi LEFT JOIN .`'.$this->table_name(1).'` AS v ON vi.voucher_id = v.voucher_id where  '.$where_str;
        $row = $this->db->selectrow($count_sql);
        $count = $row['total'];

        return $count;
    }
    public function apply_id()
    {
        $sign = '8';
        $tb = $this->table_name(1);
        do {
            $i = substr(mt_rand(), -3);
            $voucher_id = $sign.date('ymdHis').$i;
            $row = $this->db->selectrow('SELECT voucher_id from '.$tb.' where voucher_id ='.$voucher_id);
        } while ($row);

        return $voucher_id;
    }
    public function pre_recycle($rows)
    {
        $this->recycle_msg = '删除成功!';
        $voucher_id_arr = array_keys(utils::array_change_key($rows, 'voucher_id'));
        if ($this->count(array('status' => 'succ', 'voucher_id' => $voucher_id_arr))) {
            $this->recycle_msg = '无法删除已确认的凭证!';

            return false;
        }

        return true;
    }
    public function modifier_status($col)
    {
        switch ($col) {
            case 'succ':
                # code...
                return "<span class='label label-success'>已确认</span>";
            case 'ready':
                return "<span class='label bg-blue'>待结算</span>";
            case 'process':
                return "<span class='label bg-yellow'>待结算</span>";
            case 'cancel':
                return "<span class='label label-default'>已取消</span>";

        }
    }
}
