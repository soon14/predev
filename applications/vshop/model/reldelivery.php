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


class vshop_mdl_reldelivery extends dbeav_model
{
    public function getDeliveryList($shop_id, $cols = 'd.*', $filter = array(), $offset = 0, $limit = -1, $orderby = '', &$count)
    {

        $mdl_delivery = app::get('b2c')->model('delivery');
        $dbeav_filter = vmc::singleton('dbeav_filter');
        $where_str = $dbeav_filter->dbeav_filter_parser($filter, 'd', false, app::get('b2c')->model('delivery'));
        if ($shop_id) {
            if (is_array($shop_id)) {
                $where_str .= ' AND rd.shop_id IN '."('".implode("','", $shop_id)."')";
            } else {
                $where_str .= ' AND rd.shop_id = '.$shop_id;
            }
        }

        $orderby = $orderby != '' ? ' order by '.$orderby : '';
        $sql = 'select rd.shop_id,rd.shop_name,'.$cols.' from `'.$this->table_name(1)
            .'` AS rd LEFT JOIN .`'.$mdl_delivery->table_name(1).'` AS d ON rd.delivery_id = d.delivery_id where  '.$where_str.' '.$orderby;
        $count_sql = 'select count(d.delivery_id) as count from `'.$this->table_name(1)
            .'` AS rd LEFT JOIN .`'.$mdl_delivery->table_name(1).'` AS d ON rd.delivery_id = d.delivery_id where  '.$where_str;
        $rows = $this->db->selectLimit($sql, $limit, $offset);
        $row = $this->db->selectrow($count_sql);
        $count = $row['count'];

        return $rows;
    }

    public function count_delivery($shop_id, $filter = array())
    {
        $mdl_delivery = app::get('b2c')->model('delivery');
        $dbeav_filter = vmc::singleton('dbeav_filter');
        $where_str = $dbeav_filter->dbeav_filter_parser($filter, 'd', false, app::get('b2c')->model('delivery'));
        $where_str .= ' AND rd.shop_id = '.$shop_id;
        $count_sql = 'select count(d.delivery_id) as count from `'.$this->table_name(1)
            .'` AS rd LEFT JOIN .`'.$mdl_delivery->table_name(1).'` AS d ON rd.delivery_id = d.delivery_id where  '.$where_str;
        $row = $this->db->selectrow($count_sql);
        $count = $row['count'];

        return $count;
    }
}
