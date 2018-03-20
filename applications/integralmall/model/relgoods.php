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


class integralmall_mdl_relgoods extends dbeav_model
{
    public function __construct($app)
    {
        parent::__construct($app);
    }
    public function goodsList($gfilter, $rfilter, $offset, $limit, $orderby, &$total)
    {
        //logger::alert($rfilter);
        $mdl_goods = app::get('b2c')->model('goods');
        $dbeav_filter = vmc::singleton('dbeav_filter');
        $gwhere_str = $dbeav_filter->dbeav_filter_parser($gfilter, 'g', false, $mdl_goods);
        $rwhere_str = $dbeav_filter->dbeav_filter_parser($rfilter, 'r', false, $this);
        $count_SQL = 'SELECT count(r.goods_id) as count FROM vmc_integralmall_relgoods AS r LEFT JOIN vmc_b2c_goods as g ON r.goods_id = g.goods_id WHERE '.$gwhere_str.' AND '.$rwhere_str;
        if($orderby){
            $orderby = 'ORDER BY '.$orderby;
        }
        $SQL = 'SELECT r.*,g.* FROM vmc_integralmall_relgoods AS r LEFT JOIN vmc_b2c_goods as g ON r.goods_id = g.goods_id WHERE '.$gwhere_str.' AND '.$rwhere_str.' '.$orderby;
        $count_row = $this->db->selectrow($count_SQL);
        $total = $count_row['count'];
        logger::alert($SQL);
        $rows = $this->db->selectLimit($SQL, $limit, $offset);

        return $rows;
    }

    public function relcat(){
        $SQL = 'SELECT r.goods_id,g.cat_id,
        SUBSTRING_INDEX(c.cat_path,",",1) AS cat_root FROM vmc_integralmall_relgoods AS r LEFT JOIN vmc_b2c_goods as g ON r.goods_id = g.goods_id LEFT JOIN vmc_b2c_goods_cat AS c ON g.cat_id = c.cat_id GROUP BY cat_root ORDER BY c.p_order ASC';
        $rows = $this->db->select($SQL);
        $cat_root_ids =  array_keys(utils::array_change_key($rows, 'cat_root'));
        $mdl_goods_cat = app::get('b2c')->model('goods_cat');
        return $mdl_goods_cat->getList('*',array('cat_id'=>$cat_root_ids));
    }
    public function relbrand($cat_id){
        $SQL = 'SELECT r.goods_id,g.brand_id,
        b.brand_name,b.brand_logo,g.cat_id FROM vmc_integralmall_relgoods AS r LEFT JOIN vmc_b2c_goods as g ON r.goods_id = g.goods_id LEFT JOIN vmc_b2c_brand AS b ON g.brand_id = b.brand_id GROUP BY g.brand_id';
        if($cat_id){
            $cat_ids = app::get('b2c')->model('goods_cat')->get_all_children_id($cat_id);
            $dbeav_filter = vmc::singleton('dbeav_filter');
            $havingstr =  $dbeav_filter->dbeav_filter_parser(array('cat_id'=>$cat_ids), 'g', false, app::get('b2c')->model('goods'));
            $SQL.=' HAVING '.$havingstr;
        }
        logger::alert($SQL);
        return $this->db->select($SQL);
    }
}
