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


class o2ocds_mdl_orderlog_achieve extends dbeav_model
{
    public $has_tag = true;
    public $defaultOrder = array(
        ' createtime',
        'DESC',
    );
    public function apply_id($exclude_id='')
    {
        $sign = '1';
        $tb = $this->table_name(1);
        do {
            $i = substr(mt_rand(), -3);
            $achieve_id = $sign.date('ymdHis').$i;
            if($exclude_id == $achieve_id) {
                $row = true;
            }else{
                $row = $this->db->selectrow('SELECT achieve_id from '.$tb.' where achieve_id ='.$achieve_id);
            }
        } while ($row);

        return $achieve_id;
    }

    public function count_subprice($filter)
    {
        $where_str  = $this->_filter($filter);
        $count_sql = "SELECT sum(achieve_fund) as total FROM ".$this->table_name(1)." WHERE $where_str";
        $row = $this->db->selectrow($count_sql);
        $count = $row['total'];
        return $count;
    }

    /**
     * 结算凭证删除除前做的事情
     * @param array post
     * @return boolean
     */
    public function pre_recycle($rows)
    {
        //操作日志
        $obj_log = vmc::singleton('o2ocds_operatorlog');
        if(method_exists($obj_log,'achieve_log')) {
            $obj_log->achieve_log($rows);
        }

        return true;
    }

}
