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
class o2ocds_mdl_service_code extends dbeav_model
{
    public $has_tag = true;
    public $defaultOrder = array('createtime','DESC');

    /*
     * 订单列表
     * */
    public function order_list($filter = array(),  $order_filter = array(), $offset = 0, $limit = -1, $orderby = '', &$count)
    {
        $dbeav_filter = vmc::singleton('dbeav_filter');
        $where_str = '';
        //店员只查询核销订单
        if ($filter) {
            $where_str .= $this->_filter($filter, 'sc').' ';
        } else {
            return false;
        }
        if ($order_filter) {
            $where_str .= substr($dbeav_filter->dbeav_filter_parser($order_filter, 'o', false, app::get('b2c')->model('orders')), '1');
        }
        if ($orderby) {
            $orderby = 'ORDER BY '.$orderby;
        }
        $SQL = "SELECT sc.service_code as sc_code,sc.integral AS sc_integral,sc.member_id AS sc_member_id,sc.`cancel_time` as sc_cancel_time,sc.store_id,sc.enterprise_id,o.* FROM `vmc_o2ocds_service_code` sc
                LEFT JOIN `vmc_b2c_orders` o ON sc.order_id = o.order_id
                WHERE $where_str GROUP BY o.order_id $orderby LIMIT $offset,$limit";
        $SQLCount = "SELECT count(*) FROM (SELECT count(*) FROM `vmc_o2ocds_service_code` sc LEFT JOIN `vmc_b2c_orders` o ON sc.order_id = o.order_id WHERE  $where_str GROUP BY o.order_id) as ordercount";
        $count = $this->db->count($SQLCount);

        return $this->db->select($SQL);
    }

    /*
     * 商品热销排名
     * */
    public function get_hots($filter = array(), $offset = 0, $limit = -1, $orderby = '', &$count ,&$amount_avg)
    {
        $where_str = '';
        if ($filter) {
            $where_str .= $this->_filter($filter, 'sc').' ';
        } else {
            return false;
        }

        $SQL = "SELECT COUNT(boi.goods_id) AS goods_count,SUM(boi.`buy_price`) AS goods_sum,boi.*
                FROM `vmc_o2ocds_service_code` sc
                LEFT JOIN `vmc_b2c_order_items` boi ON boi.order_id = sc.order_id
                WHERE $where_str AND item_type = 'product' GROUP BY boi.goods_id  ";

        if ($orderby) {
            $SQL .= " ORDER BY {$orderby} DESC ";
        }else{
            $SQL .= " ORDER BY sc.createtime DESC ";
        }
        if($limit > 1) {
            $SQL .= " LIMIT $offset,$limit ";
        }

        $SQLCount = "SELECT count(*) FROM (SELECT count(*) FROM `vmc_o2ocds_service_code` sc LEFT JOIN `vmc_b2c_order_items` boi ON boi.order_id = sc.order_id WHERE  $where_str AND item_type = 'product' GROUP BY boi.goods_id) as goods_count";
        $count = $this->db->count($SQLCount);

        $SQLAvg = "SELECT round(avg(`buy_price`),2) as amount_avg FROM `vmc_o2ocds_service_code` sc LEFT JOIN `vmc_b2c_order_items` boi ON boi.order_id = sc.order_id WHERE  $where_str AND item_type = 'product'";
        $amount_avg = $this->db->select($SQLAvg)[0]['amount_avg'];

        return $this->db->select($SQL);
    }


    public function apply_code()
    {
        $tb = $this->table_name(1);
        do {
            $new_code = $this->rand_str(6);
            $row = $this->db->selectrow('SELECT service_code from '.$tb.' where service_code ='.$new_code);
        } while ($row);

        return $new_code;
    }

    public function modifier_member_id($col)
    {
        if ($members = app::get('pam')->model('members')->getRow('login_account', array('member_id' => $col))) {
            return $members['login_account'];
        };

        return '';
    }

    // public function modifier_store_id($col) {
    //     if($store = $this->app->model('store')->getRow('name',array('store_id'=>$col))) {
    //         return $store['name'];
    //     };
    //     return '';
    // }
    //
    // public function modifier_enterprise_id($col) {
    //     if($enterprise = $this->app->model('enterprise')->getRow('name',array('enterprise_id'=>$col))) {
    //         return $enterprise['name'];
    //     };
    //     return '';
    // }

    public function subprice($filter)
    {
        $where_str = $this->_filter($filter, 'sc');
        $SQL = 'SELECT sum(o.order_total) as subprice FROM vmc_b2c_orders as o RIGHT JOIN vmc_o2ocds_service_code as sc ON o.order_id = sc.order_id WHERE (o.pay_status = 2 or o.is_cod="Y") AND '.$where_str;
        //echo $SQL;
        $res = $this->db->selectrow($SQL);
        if ($res) {
            return $res['subprice'];
        }

        return 0;
    }

    public function rand_str($length)
    {
        $str = '0123456789X';//10个字符
        $strlen = 10;
        while ($length > $strlen) {
            $str .= $str;
            $strlen += 10;
        }
        $str = str_shuffle($str);

        return substr($str, 0, $length);
    }

    /*
     * @params member_id
     * 会员是否渠道价身份
     * return true 是渠道价身份，false 不是渠道价身份
     * */
    public function member_channelprice($member_id) {
        if($member_id) {
            $member_lv_id = app::get('b2c')->model('members')->getRow('member_lv_id',array('member_id'=>$member_id))['member_lv_id'];
            if(!$member_lv = app::get('b2c')->model('member_lv')->getRow('*',array('member_lv_id'=>$member_lv_id))) {
                return false;
            };
            //不等于不开放渠道价身份 （那就是渠道价身份）
            if($member_lv['channelprice'] != '0') {
                return true;
            }
        }
        return false;
    }

}
