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


class supplier_mdl_relgoods extends dbeav_model
{
    public function count_relgoods($supplier_id, $filter = array())
    {
        $mdl_product = app::get('b2c')->model('products');
        $dbeav_filter = vmc::singleton('dbeav_filter');
        $where_str = $dbeav_filter->dbeav_filter_parser($filter, 'p', false, $mdl_product);
        if($supplier_id){
            if(is_array($supplier_id)){
                $where_str .= ' AND rg.supplier_id IN '."('" . implode("','", $supplier_id) . "')";
            }else{
                $where_str .= ' AND rg.supplier_id = '.$supplier_id;
            }
        }
        $count_sql = 'select count(p.product_id) as count from `'.$this->table_name(1)
            .'` AS rg LEFT JOIN .`'.$mdl_product->table_name(1).'` AS p ON rg.product_id = p.product_id where  '.$where_str;

        $row = $this->db->selectrow($count_sql);
        $count = $row['count'];

        return $count;
    }

    public function get_products($supplier_id, $cols = 'p.*', $filter = array(), $offset = 0, $limit = -1, $orderby = '')
    {
        $mdl_product = app::get('b2c')->model('products');
        $dbeav_filter = vmc::singleton('dbeav_filter');
        $where_base = array('supplier_id' => $supplier_id);
        $where_str = $dbeav_filter->dbeav_filter_parser($filter, 'p', false, $mdl_product);
        $where_str .= ' AND rg.supplier_id = '.$supplier_id;
        $orderby = $orderby != '' ? ' order by '.$orderby : '';
        $sql = 'select rg.supplier_id,rg.purchase_price,'.$cols.' from `'.$this->table_name(1)
            .'` AS rg LEFT JOIN .`'.$mdl_product->table_name(1).'` AS p ON rg.product_id = p.product_id where  '.$where_str.' '.$orderby;

        $rows = $this->db->selectLimit($sql, $limit, $offset);

        return $rows;
    }
}
