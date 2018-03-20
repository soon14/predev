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


class vshop_mdl_relorder extends dbeav_model
{
    var $has_tag = true;

    public function __construct($app)
    {
        parent::__construct($app);

    }
    public function getRelOrderList($shop_id, $cols = 'o.*', $filter = array(), $offset = 0, $limit = -1, $orderby = '', &$count)
    {

        $mdl_orders = app::get('b2c')->model('orders');
        $dbeav_filter = vmc::singleton('dbeav_filter');
        $where_str = $dbeav_filter->dbeav_filter_parser($filter, 'o', false, app::get('b2c')->model('orders'));
        if ($shop_id) {
            if (is_array($shop_id)) {
                $where_str .= ' AND ro.shop_id IN '."('".implode("','", $shop_id)."')";
            } else {
                $where_str .= ' AND ro.shop_id = '.$shop_id;
            }
        }

        $orderby = $orderby != '' ? ' order by '.$orderby : '';
        $sql = 'select ro.shop_id,ro.shop_name,'.$cols.' from `'.$this->table_name(1)
            .'` AS ro LEFT JOIN .`'.$mdl_orders->table_name(1).'` AS o ON ro.order_id = o.order_id where  '.$where_str.' '.$orderby;
        $count_sql = 'select count(o.order_id) as count from `'.$this->table_name(1)
            .'` AS ro LEFT JOIN .`'.$mdl_orders->table_name(1).'` AS o ON ro.order_id = o.order_id where  '.$where_str;
        $rows = $this->db->selectLimit($sql, $limit, $offset);
        $row = $this->db->selectrow($count_sql);
        $count = $row['count'];

        return $rows;
    }
    public function modifier_order_id($col){

        return '<a target="_blank" href="index.php?app=b2c&ctl=admin_order&act=detail&p[0]='.$col.'">'.$col.'</a>';
    }
}
